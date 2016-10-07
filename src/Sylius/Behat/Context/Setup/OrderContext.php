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
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

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
     * @var OrderProcessorInterface
     */
    private $orderProcessor;

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $itemQuantityModifier;

    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var FactoryInterface
     */
    private $customerFactory;

    /**
     * @var RepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRepositoryInterface $orderRepository
     * @param FactoryInterface $orderFactory
     * @param OrderProcessorInterface $orderProcessor
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $itemQuantityModifier
     * @param RepositoryInterface $currencyRepository
     * @param CurrencyStorageInterface $currencyStorage
     * @param FactoryInterface $customerFactory
     * @param RepositoryInterface $customerRepository
     * @param ObjectManager $objectManager
     * @param StateMachineFactoryInterface $stateMachineFactory
     * @param ProductVariantResolverInterface $variantResolver
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderProcessorInterface $orderProcessor,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        RepositoryInterface $currencyRepository,
        CurrencyStorageInterface $currencyStorage,
        FactoryInterface $customerFactory,
        RepositoryInterface $customerRepository,
        ObjectManager $objectManager,
        StateMachineFactoryInterface $stateMachineFactory,
        ProductVariantResolverInterface $variantResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderProcessor = $orderProcessor;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->currencyRepository = $currencyRepository;
        $this->currencyStorage = $currencyStorage;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->objectManager = $objectManager;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->variantResolver = $variantResolver;
    }

    /**
     * @Given /^there is (?:a|another) (customer "[^"]+") that placed an order$/
     * @Given /^there is (?:a|another) (customer "[^"]+") that placed (an order "[^"]+")$/
     * @Given a customer :customer placed an order :orderNumber
     * @Given the customer :customer has already placed an order :orderNumber
     */
    public function thereIsCustomerThatPlacedOrder(CustomerInterface $customer, $orderNumber = null)
    {
        $order = $this->createOrder($customer, $orderNumber);

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given a customer :customer added something to cart
     */
    public function customerStartedCheckout(CustomerInterface $customer)
    {
        $cart = $this->createCart($customer);

        $this->sharedStorage->set('cart', $cart);

        $this->orderRepository->add($cart);
    }

    /**
     * @Given /^(I) placed (an order "[^"]+")$/
     */
    public function iPlacedAnOrder(UserInterface $user, $orderNumber)
    {
        $customer = $user->getCustomer();
        $order = $this->createOrder($customer, $orderNumber);

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^the customer ("[^"]+" addressed it to "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+"))$/
     * @Given /^I (addressed it to "[^"]+", "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+"))$/
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
     * @Given /^for the billing address (of "[^"]+" in the "[^"]+", "[^"]+" "([^"]+)", "[^"]+", "[^"]+")$/
     */
    public function forTheBillingAddressOf(AddressInterface $address)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $order->setBillingAddress($address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer ("[^"]+" addressed it to "[^"]+", "[^"]+" "[^"]+" in the "[^"]+") with identical billing address$/
     * @Given /^I (addressed it to "[^"]+", "[^"]+", "[^"]+" "[^"]+" in the "[^"]+") with identical billing address$/
     */
    public function theCustomerAddressedItToWithIdenticalBillingAddress(AddressInterface $address)
    {
        $this->theCustomerAddressedItTo($address);
        $this->forTheBillingAddressOf($address);
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

        $order->setShippingAddress($address);
        $order->setBillingAddress($address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->orderProcessor->process($order);
        $order->getShipments()->first()->setMethod($shippingMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $payment = $order->getLastNewPayment();
        $payment->setMethod($paymentMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer has chosen to order in the :currencyCode currency
     * @Given I have chosen to order in the :currencyCode currency
     */
    public function theCustomerChoseTheCurrency($currencyCode)
    {
        $this->currencyStorage->set($this->sharedStorage->get('channel'), $currencyCode);

        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $order->setCurrencyCode($currencyCode);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) with ("[^"]+" payment)$/
     * @Given /^I chose ("[^"]+" shipping method) with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingWithPayment(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderProcessor->process($order);
        $order->getShipments()->first()->setMethod($shippingMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $payment = $order->getLastNewPayment();
        $payment->setMethod($paymentMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product
     * @Given I bought a single :product
     */
    public function theCustomerBoughtSingleProduct(ProductInterface $product)
    {
        $this->addProductVariantToOrder($this->variantResolver->getVariant($product), $product->getPrice(), 1);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ((?:a|an) "[^"]+") and ((?:a|an) "[^"]+")$/
     * @Given /^I bought ((?:a|an) "[^"]+") and ((?:a|an) "[^"]+")$/
     */
    public function theCustomerBoughtProductAndProduct(ProductInterface $product, ProductInterface $secondProduct)
    {
        $this->theCustomerBoughtSingleProduct($product);
        $this->theCustomerBoughtSingleProduct($secondProduct);
    }

    /**
     * @Given /^the customer bought (\d+) ("[^"]+" products)$/
     */
    public function theCustomerBoughtSeveralProducts($quantity, ProductInterface $product)
    {
        $variant = $this->variantResolver->getVariant($product);
        $this->addProductVariantToOrder($variant, $variant->getPrice(), $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ([^"]+) items of ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSeveralVariantsOfProduct($quantity, ProductVariantInterface $variant)
    {
        $this->addProductVariantToOrder($variant, $variant->getPrice(), $quantity);

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
    public function theCustomerBoughtSingleUsing(ProductInterface $product, PromotionCouponInterface $coupon)
    {
        $order = $this->addProductVariantToOrder($this->variantResolver->getVariant($product), $product->getPrice());
        $order->setPromotionCoupon($coupon);

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
     * @Given the order :order is already paid
     */
    public function thisOrderIsAlreadyPaid(OrderInterface $order)
    {
        $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer cancelled (this order)$/
     * @Given /^(this order) was cancelled$/
     * @Given the order :order was cancelled
     */
    public function theCustomerCancelledThisOrder(OrderInterface $order)
    {
        $this->stateMachineFactory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::TRANSITION_CANCEL);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) has already been shipped$/
     */
    public function thisOrderHasAlreadyBeenShipped(OrderInterface $order)
    {
        $this->applyShipmentTransitionOnOrder($order, ShipmentTransitions::TRANSITION_SHIP);

        $this->objectManager->flush();
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    private function applyShipmentTransitionOnOrder(OrderInterface $order, $transition)
    {
        foreach ($order->getShipments() as $shipment) {
            $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->apply($transition);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    private function applyPaymentTransitionOnOrder(OrderInterface $order, $transition)
    {
        foreach ($order->getPayments() as $payment) {
            $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply($transition);
        }
    }

    /**
     * @param OrderInterface $order
     * @param string $transition
     */
    private function applyTransitionOnOrderCheckout(OrderInterface $order, $transition)
    {
        $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
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

        return $order;
    }

    /**
     * @param CustomerInterface $customer
     * @param string $number
     * @param ChannelInterface|null $channel
     * @param string|null $currencyCode
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createOrder(
        CustomerInterface $customer,
        $number = null,
        ChannelInterface $channel = null,
        $currencyCode = null,
        $localeCode = null
    ) {
        $order = $this->createCart($customer, $channel, $currencyCode, $localeCode);

        if (null !== $number) {
            $order->setNumber($number);
        }

        $order->complete();

        return $order;
    }

    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface|null $channel
     * @param string|null $currencyCode
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createCart(
        CustomerInterface $customer,
        ChannelInterface $channel = null,
        $currencyCode = null,
        $localeCode = null
    ) {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setChannel((null !== $channel) ? $channel : $this->sharedStorage->get('channel'));
        $order->setCurrencyCode((null !== $currencyCode) ? $currencyCode : $this->sharedStorage->get('currency')->getCode());
        $order->setLocaleCode((null !== $localeCode) ? $localeCode : $this->sharedStorage->get('locale')->getCode());

        $currencyCode = $currencyCode ? $currencyCode : $this->sharedStorage->get('currency')->getCode();
        $currency = $this->currencyRepository->findOneBy(['code' => $currencyCode]);

        $order->setCurrencyCode($currency->getCode());
        $order->setExchangeRate($currency->getExchangeRate());

        return $order;
    }

    /**
     * @param int $count
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
