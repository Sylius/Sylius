<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var OrderShipmentProcessorInterface
     */
    private $orderShipmentFactory;

    /**
     * @var PaymentFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $itemQuantityModifier;

    /**
     * @var OrderRecalculatorInterface
     */
    private $orderRecalculator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRepositoryInterface $orderRepository
     * @param FactoryInterface $orderFactory
     * @param OrderShipmentProcessorInterface $orderShipmentFactory
     * @param PaymentFactoryInterface $paymentFactory
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $itemQuantityModifier
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRecalculatorInterface $orderRecalculator
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderShipmentProcessorInterface $orderShipmentFactory,
        PaymentFactoryInterface $paymentFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderRecalculatorInterface $orderRecalculator,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderShipmentFactory = $orderShipmentFactory;
        $this->paymentFactory = $paymentFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->orderRecalculator = $orderRecalculator;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is a customer :customer that placed an order :orderNumber
     */
    public function thereIsCustomerThatPlacedOrder(CustomerInterface $customer, $orderNumber)
    {
        $order = $this->createOrder($customer, $orderNumber);

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^the customer ("[^"]+" addressed it to "[^"]+", "[^"]+" "[^"]+" in the "[^"]+")$/
     */
    public function theCustomerAddressedItTo(AddressInterface $address)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $order->setShippingAddress($address);

        $this->objectManager->flush();
    }

    /**
     * @Given /^for the billing address (of "[^"]+" in the "[^"]+", "[^"]+" "[^"]+", "[^"]+")$/
     */
    public function forTheBillingAddressOf(AddressInterface $address)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $order->setBillingAddress($address);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingToWithPayment(
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderShipmentFactory->processOrderShipment($order);
        $order->getShipments()->first()->setMethod($shippingMethod);

        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());
        $payment->setMethod($paymentMethod);

        $order->addPayment($payment);

        $order->setShippingAddress($address);
        $order->setBillingAddress($address);

        $this->orderRecalculator->recalculate($order);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingWithPayment(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderShipmentFactory->processOrderShipment($order);
        $order->getShipments()->first()->setMethod($shippingMethod);

        $this->orderRecalculator->recalculate($order);

        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());
        $payment->setMethod($paymentMethod);

        $order->addPayment($payment);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product
     */
    public function theCustomerBoughtSingleProduct(ProductInterface $product)
    {
        $this->addProductVariantToOrder($product->getMasterVariant(), $product->getPrice(), 1);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ((?:a|an) "[^"]+") and ((?:a|an) "[^"]+")$/
     */
    public function theCustomerBoughtProductAndProduct(ProductInterface $product, ProductInterface $secondProduct)
    {
        $this->theCustomerBoughtSingleProduct($product);
        $this->theCustomerBoughtSingleProduct($secondProduct);
    }


    /**
     * @Given /^the customer bought (\d+) ("[^"]+" products)/
     */
    public function theCustomerBoughtSeveralProducts($quantity, ProductInterface $product)
    {
        $this->addProductVariantToOrder($product->getMasterVariant(), $product->getPrice(), $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought a single ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSingleProductVariant(ProductVariantInterface $productVariant)
    {
        $this->addProductVariantToOrder($productVariant, $productVariant->getPrice());

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product using :coupon coupon
     */
    public function theCustomerBoughtSingleUsing(ProductInterface $product, CouponInterface $coupon)
    {
        $order = $this->addProductVariantToOrder($product->getMasterVariant(), $product->getPrice());
        $order->setPromotionCoupon($coupon);

        $this->orderRecalculator->recalculate($order);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(I) have already placed an order (\d+) times$/
     */
    public function iHaveAlreadyPlacedOrderNthTimes(UserInterface $user, $numberOfOrders)
    {
        $customer = $user->getCustomer();
        for ($i = 0; $i < $numberOfOrders; $i++) {
            $order = $this->createOrder($customer, '#00000'.$i);
            $order->setPaymentState(PaymentInterface::STATE_COMPLETED);
            $order->setCompletedAt(new \DateTime());

            $this->orderRepository->add($order);
        }
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param int $price
     * @param int $quantity
     *
     * @return OrderInterface
     */
    private function addProductVariantToOrder(ProductVariantInterface $productVariant, $price, $quantity = 1)
    {
        $order = $this->sharedStorage->get('order');

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($productVariant);
        $item->setUnitPrice($price);

        $this->itemQuantityModifier->modify($item, $quantity);

        $order->addItem($item);

        $this->orderRecalculator->recalculate($order);

        return $order;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $number
     * @param ChannelInterface|null $channel
     * @param CurrencyInterface|null $currency
     *
     * @return OrderInterface
     */
    private function createOrder(
        CustomerInterface $customer,
        $number,
        ChannelInterface $channel = null,
        CurrencyInterface $currency = null
    ) {
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setNumber($number);
        $order->setChannel((null !== $channel) ? $channel : $this->sharedStorage->get('channel'));
        $order->setCurrency((null !== $currency) ? $currency : $this->sharedStorage->get('currency'));
        $order->complete();

        return $order;
    }
}
