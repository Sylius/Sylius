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
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Core\OrderProcessing\OrderRecalculatorInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessorInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\CustomerInterface;

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
     * @var ShippingChargesProcessorInterface
     */
    private $shippingChargesProcessor;

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
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
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
        ShippingChargesProcessorInterface $shippingChargesProcessor,
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
        $this->shippingChargesProcessor = $shippingChargesProcessor;
        $this->orderRecalculator = $orderRecalculator;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the customer :customer placed an order :orderNumber
     */
    public function theCustomerPlacedAnOrder(CustomerInterface $customer, $orderNumber)
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setNumber($orderNumber);
        $order->setChannel($this->sharedStorage->get('channel'));
        $order->setCurrency($this->sharedStorage->get('currency'));

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
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

        $this->shippingChargesProcessor->applyShippingCharges($order);

        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());
        $payment->setMethod($paymentMethod);

        $order->addPayment($payment);

        $order->setShippingAddress($address);
        $order->setBillingAddress($address);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought single :product
     */
    public function theCustomerBoughtSingleProduct(ProductInterface $product)
    {
        $this->addSingleProductVariantToOrder($product->getMasterVariant(), $product->getPrice());
    }

    /**
     * @Given /^the customer bought single ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSingleProductVariant(ProductVariantInterface $productVariant)
    {
        $this->addSingleProductVariantToOrder($productVariant, $productVariant->getPrice());
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param int $price
     */
    private function addSingleProductVariantToOrder(ProductVariantInterface $productVariant, $price)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($productVariant);
        $item->setUnitPrice($price);

        $this->itemQuantityModifier->modify($item, 1);

        $order->addItem($item);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought single :product using :coupon coupon
     */
    public function theCustomerBoughtSingleUsing(ProductInterface $product, CouponInterface $coupon)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($product->getMasterVariant());
        $item->setUnitPrice($product->getPrice());

        $this->itemQuantityModifier->modify($item, 1);

        $order->setPromotionCoupon($coupon);
        $order->addItem($item);

        $this->orderRecalculator->recalculate($order);
        $this->objectManager->flush();
    }
}
