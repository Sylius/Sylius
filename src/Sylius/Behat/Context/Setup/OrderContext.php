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
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
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
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
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
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRecalculatorInterface
     */
    private $orderRecalculator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRepositoryInterface $orderRepository
     * @param FactoryInterface $orderFactory
     * @param OrderShipmentProcessorInterface $orderShipmentFactory
     * @param PaymentFactoryInterface $paymentFactory
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $itemQuantityModifier
     * @param FactoryInterface $customerFactory
     * @param RepositoryInterface $customerRepository
     * @param OrderRecalculatorInterface $orderRecalculator
     * @param ObjectManager $objectManager
     * @param StateMachineFactoryInterface $stateMachineFactory
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderShipmentProcessorInterface $orderShipmentFactory,
        PaymentFactoryInterface $paymentFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        FactoryInterface $customerFactory,
        RepositoryInterface $customerRepository,
        OrderRecalculatorInterface $orderRecalculator,
        ObjectManager $objectManager,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderShipmentFactory = $orderShipmentFactory;
        $this->paymentFactory = $paymentFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderRecalculator = $orderRecalculator;
        $this->objectManager = $objectManager;
        $this->stateMachineFactory = $stateMachineFactory;
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
        $this->addProductVariantToOrder($product->getFirstVariant(), $product->getPrice(), 1);

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
        $this->addProductVariantToOrder($product->getFirstVariant(), $product->getPrice(), $quantity);

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
        $order = $this->addProductVariantToOrder($product->getFirstVariant(), $product->getPrice());
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
     * @Given :numberOfCustomers customers have added products to the cart for total of :total
     */
    public function customersHaveAddedProductsToTheCartForTotalOf($numberOfCustomers, $total)
    {
        $customers = $this->generateCustomers($numberOfCustomers);

        $sampleProductVariant = $this->sharedStorage->get('variant');
        $total = $this->getPriceFromString($total);

        for ($i = 0; $i < $numberOfCustomers; $i++) {
            $order = $this->createOrder($customers[rand(0, $numberOfCustomers - 1)]);
            $order->setCompletedAt(null);

            $price = $i === ($numberOfCustomers - 1) ? $total : rand(1, $total);
            $total -= $price;

            $item = $this->orderItemFactory->createNew();
            $item->setVariant($sampleProductVariant);
            $item->setUnitPrice($price);

            $this->itemQuantityModifier->modify($item, 1);

            $order->addItem($item);

            $this->orderRepository->add($order);
        }
    }

    /**
     * @Given :numberOfCustomers customers have placed :numberOfOrders orders for total of :total
     * @Given then :numberOfCustomers more customers have placed :numberOfOrders orders for total of :total
     */
    public function customersHavePlacedOrdersForTotalOf($numberOfCustomers, $numberOfOrders, $total)
    {
        $customers = $this->generateCustomers($numberOfCustomers);
        $sampleProductVariant = $this->sharedStorage->get('variant');
        $total = $this->getPriceFromString($total);

        for ($i = 0; $i < $numberOfOrders; $i++) {
            $order = $this->createOrder($customers[rand(0, $numberOfCustomers - 1)], '#'.uniqid());
            $order->setPaymentState(PaymentInterface::STATE_COMPLETED);
            $order->setCompletedAt(new \DateTime());

            $price = $i === ($numberOfOrders - 1) ? $total : rand(1, $total);
            $total -= $price;

            $item = $this->orderItemFactory->createNew();
            $item->setVariant($sampleProductVariant);
            $item->setUnitPrice($price);

            $this->itemQuantityModifier->modify($item, 1);

            $order->addItem($item);

            $this->orderRepository->add($order);
        }
    }

    /**
     * @Given /^(this order) is already paid$/
     */
    public function thisOrderIsAlreadyPaid(OrderInterface $order)
    {
        $payment = $order->getLastPayment();
        $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply(PaymentTransitions::SYLIUS_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer canceled (this order)$/
     */
    public function theCustomerCanceledThisOrder(OrderInterface $order)
    {
        $this->stateMachineFactory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CANCEL);
        
        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer confirmed (this order)$/
     */
    public function theCustomerConfirmedThisOrder(OrderInterface $order)
    {
        $this->stateMachineFactory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CONFIRM);
       
        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) is ready to ship$/
     */
    public function thisOrderIsReadyToShip(OrderInterface $order)
    {
        $this->applyShipmentTransitionOnOrder($order, ShipmentTransitions::SYLIUS_PREPARE);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) has already been shipped$/
     */
    public function thisOrderHasAlreadyBeenShipped(OrderInterface $order)
    {
        $this->applyShipmentTransitionOnOrder($order, ShipmentTransitions::SYLIUS_PREPARE);
        $this->applyShipmentTransitionOnOrder($order, ShipmentTransitions::SYLIUS_SHIP);

        $this->objectManager->flush();
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    private function applyShipmentTransitionOnOrder(OrderInterface $order, $transition)
    {
        $shipment = $order->getShipments()->first();
        $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->apply($transition);
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
        $item->setUnitPrice($productVariant->getPrice());

        $this->itemQuantityModifier->modify($item, $quantity);

        $order->addItem($item);

        $this->orderRecalculator->recalculate($order);

        return $order;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $number
     * @param ChannelInterface|null $channel
     * @param string|null $currencyCode
     *
     * @return OrderInterface
     */
    private function createOrder(
        CustomerInterface $customer,
        $number = null,
        ChannelInterface $channel = null,
        $currencyCode = null
    ) {
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setNumber($number);
        $order->setChannel((null !== $channel) ? $channel : $this->sharedStorage->get('channel'));
        $order->setCurrency((null !== $currencyCode) ? $currencyCode : $this->sharedStorage->get('currency')->getCode());
        $order->complete();

        return $order;
    }

    /**
     * @param $count
     *
     * @return CustomerInterface[]
     */
    private function generateCustomers($count)
    {
        $customers = [];

        for ($i = 0; $i < $count; $i++) {
            $customer = $this->customerFactory->createNew();
            $customer->setEmail(sprintf('john%s@doe.com', uniqid()));
            $customer->setFirstname('John');
            $customer->setLastname('Doe'.$i);

            $customers[] = $customer;

            $this->customerRepository->add($customer);
        }

        return $customers;
    }

    /**
     * @param string $price
     *
     * @return int
     */
    private function getPriceFromString($price)
    {
        return (int) round((str_replace(['€', '£', '$'], '', $price) * 100), 2);
    }
}
