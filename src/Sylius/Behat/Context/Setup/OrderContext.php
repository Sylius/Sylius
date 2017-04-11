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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
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
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $itemQuantityModifier
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
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        FactoryInterface $customerFactory,
        RepositoryInterface $customerRepository,
        ObjectManager $objectManager,
        StateMachineFactoryInterface $stateMachineFactory,
        ProductVariantResolverInterface $variantResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
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
     * @Given the customer changed shipping address' street to :street
     */
    public function theCustomerChangedShippingAddressStreetTo($street)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $shippingAddress = $order->getShippingAddress();
        $shippingAddress->setStreet($street);

        $this->objectManager->flush();

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);
    }

    /**
     * @Given /^the customer set the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
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
        $this->forTheBillingAddressOf(clone $address);
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     * @Given /^I chose ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingToWithPayment(
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->checkoutUsing($order, $shippingMethod, $address, $paymentMethod);

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

        $this->proceedSelectingShippingAndPaymentMethod($order, $shippingMethod, $paymentMethod);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method)$/
     */
    public function theCustomerChoseShippingMethod(ShippingMethodInterface $shippingMethod)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
        $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH)->apply(OrderPaymentTransitions::TRANSITION_PAY);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product
     * @Given I bought a single :product
     */
    public function theCustomerBoughtSingleProduct(ProductInterface $product)
    {
        $this->addProductVariantToOrder($this->variantResolver->getVariant($product), 1);

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
        $this->addProductVariantToOrder($variant, $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ([^"]+) units of ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSeveralVariantsOfProduct($quantity, ProductVariantInterface $variant)
    {
        $this->addProductVariantToOrder($variant, $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought a single ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSingleProductVariant(ProductVariantInterface $productVariant)
    {
        $this->addProductVariantToOrder($productVariant);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product using :coupon coupon
     * @Given I bought a single :product using :coupon coupon
     */
    public function theCustomerBoughtSingleUsing(ProductInterface $product, PromotionCouponInterface $coupon)
    {
        $order = $this->addProductVariantToOrder($this->variantResolver->getVariant($product));
        $order->setPromotionCoupon($coupon);

        $this->objectManager->flush();
    }

    /**
     * @Given I used :coupon coupon
     */
    public function iUsedCoupon(PromotionCouponInterface $coupon)
    {
        $order = $this->sharedStorage->get('order');
        $order->setPromotionCoupon($coupon);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(I) have already placed (\d+) orders choosing ("[^"]+" product), ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     */
    public function iHaveAlreadyPlacedOrderNthTimes(
        UserInterface $user,
        $numberOfOrders,
        ProductInterface $product,
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod
    ) {
        $customer = $user->getCustomer();
        for ($i = 0; $i < $numberOfOrders; $i++) {
            /** @var ProductVariantInterface $variant */
            $variant = $this->variantResolver->getVariant($product);

            /** @var ChannelPricingInterface $channelPricing */
            $channelPricing = $variant->getChannelPricingForChannel($this->sharedStorage->get('channel'));

            /** @var \Sylius\Component\Order\Model\OrderItemInterface $item */
            $item = $this->orderItemFactory->createNew();
            $item->setVariant($variant);
            $item->setUnitPrice($channelPricing->getPrice());

            $this->itemQuantityModifier->modify($item, 1);

            $order = $this->createOrder($customer, '#00000'.$i);
            $order->addItem($item);

            $this->checkoutUsing($order, $shippingMethod, clone $address, $paymentMethod);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $this->objectManager->persist($order);
            $this->sharedStorage->set('order', $order);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") at "([^"]+)"$/
     */
    public function thisCustomerHasPlacedAnOrderAtDate(CustomerInterface $customer, $number, $checkoutCompletedAt)
    {
        $order = $this->createOrder($customer, $number);
        $order->setCheckoutCompletedAt(new \DateTime($checkoutCompletedAt));
        $order->setState(OrderInterface::STATE_NEW);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") on a (channel "[^"]+")$/
     */
    public function thisCustomerHasPlacedAnOrderOnAChannel(CustomerInterface $customer, $number, $channel)
    {
        $order = $this->createOrder($customer, $number, $channel);
        $order->setState(OrderInterface::STATE_NEW);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^(customer "[^"]+"|this customer) has(?:| also) placed (\d+) orders on the ("[^"]+" channel) in each buying (\d+) ("[^"]+" products?)$/
     */
    public function thisCustomerPlacedOrdersOnChannelBuyingProducts(
        CustomerInterface $customer,
        $orderCount,
        ChannelInterface $channel,
        $productCount,
        ProductInterface $product
    ) {
        for ($i = 0; $i < $orderCount; $i++) {
            $order = $this->createOrder($customer, uniqid('#'), $channel);

            $this->addProductVariantsToOrderWithChannelPrice(
                $order,
                $channel,
                $this->variantResolver->getVariant($product),
                $productCount
            );

            $order->setState(OrderInterface::STATE_NEW);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
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
            $order = $this->createCart($customers[rand(0, $numberOfCustomers - 1)]);

            $price = $i === ($numberOfCustomers - 1) ? $total : rand(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
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
            $order->setState(OrderInterface::STATE_NEW); // Temporary, we should use checkout to place these orders.
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $price = $i === ($numberOfOrders - 1) ? $total : rand(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            $this->objectManager->persist($order);
            $this->sharedStorage->set('order', $order);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given :numberOfCustomers customers have placed :numberOfOrders orders for total of :total mostly :product product
     * @Given then :numberOfCustomers more customers have placed :numberOfOrders orders for total of :total mostly :product product
     */
    public function customersHavePlacedOrdersForTotalOfMostlyProduct($numberOfCustomers, $numberOfOrders, $total, ProductInterface $product)
    {
        $customers = $this->generateCustomers($numberOfCustomers);
        $sampleProductVariant = $product->getVariants()->first();
        $total = $this->getPriceFromString($total);

        for ($i = 0; $i < $numberOfOrders; $i++) {
            $order = $this->createOrder($customers[rand(0, $numberOfCustomers - 1)], '#'.uniqid(), $product->getChannels()->first());
            $order->setState(OrderInterface::STATE_NEW);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $price = $i === ($numberOfOrders - 1) ? $total : rand(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") buying a single ("[^"]+" product) for ("[^"]+") on the ("[^"]+" channel)$/
     */
    public function customerHasPlacedAnOrderBuyingASingleProductForOnTheChannel(
        CustomerInterface $customer,
        $orderNumber,
        ProductInterface $product,
        $price,
        ChannelInterface $channel
    ) {
        $order = $this->createOrder($customer, $orderNumber, $channel);
        $order->setState(OrderInterface::STATE_NEW);

        $this->addVariantWithPriceToOrder($order, $product->getVariants()->first(), $price);

        $this->orderRepository->add($order);
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
     * @Given /^(this order) has been refunded$/
     */
    public function thisOrderHasBeenRefunded(OrderInterface $order)
    {
        $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_REFUND);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer cancelled (this order)$/
     * @Given /^(this order) was cancelled$/
     * @Given the order :order was cancelled
     * @Given /^I cancelled (this order)$/
     */
    public function theCustomerCancelledThisOrder(OrderInterface $order)
    {
        $this->stateMachineFactory->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::TRANSITION_CANCEL);

        $this->objectManager->flush();
    }

    /**
     * @Given /^I cancelled my last order$/
     */
    public function theCustomerCancelledMyLastOrder()
    {
        $order = $this->sharedStorage->get('order');
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
     * @param int $quantity
     *
     * @return OrderInterface
     */
    private function addProductVariantToOrder(ProductVariantInterface $productVariant, $quantity = 1)
    {
        $order = $this->sharedStorage->get('order');

        $this->addProductVariantsToOrderWithChannelPrice(
            $order,
            $this->sharedStorage->get('channel'),
            $productVariant,
            $quantity
        );

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param ChannelInterface $channel
     * @param ProductVariantInterface $productVariant
     * @param int $quantity
     */
    private function addProductVariantsToOrderWithChannelPrice(
        OrderInterface $order,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        $quantity = 1
    ) {
        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($productVariant);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        $item->setUnitPrice($channelPricing->getPrice());

        $this->itemQuantityModifier->modify($item, $quantity);

        $order->addItem($item);
    }

    /**
     * @param CustomerInterface $customer
     * @param string $number
     * @param ChannelInterface|null $channel
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createOrder(
        CustomerInterface $customer,
        $number = null,
        ChannelInterface $channel = null,
        $localeCode = null
    ) {
        $order = $this->createCart($customer, $channel, $localeCode);

        if (null !== $number) {
            $order->setNumber($number);
        }

        $order->completeCheckout();

        return $order;
    }

    /**
     * @param CustomerInterface $customer
     * @param ChannelInterface|null $channel
     * @param string|null $localeCode
     *
     * @return OrderInterface
     */
    private function createCart(
        CustomerInterface $customer,
        ChannelInterface $channel = null,
        $localeCode = null
    ) {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setChannel((null !== $channel) ? $channel : $this->sharedStorage->get('channel'));
        $order->setLocaleCode((null !== $localeCode) ? $localeCode : $this->sharedStorage->get('locale')->getCode());
        $order->setCurrencyCode($order->getChannel()->getBaseCurrency()->getCode());

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
        return (int) round(str_replace(['€', '£', '$'], '', $price) * 100, 2);
    }

    /**
     * @param OrderInterface $order
     * @param ShippingMethodInterface $shippingMethod
     * @param AddressInterface $address
     * @param PaymentMethodInterface $paymentMethod
     */
    private function checkoutUsing(
        OrderInterface $order,
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod
    ) {
        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->proceedSelectingShippingAndPaymentMethod($order, $shippingMethod, $paymentMethod);
    }

    /**
     * @param OrderInterface $order
     * @param ShippingMethodInterface $shippingMethod
     * @param PaymentMethodInterface $paymentMethod
     */
    private function proceedSelectingShippingAndPaymentMethod(OrderInterface $order, ShippingMethodInterface $shippingMethod, PaymentMethodInterface $paymentMethod)
    {
        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $payment = $order->getLastPayment(PaymentInterface::STATE_CART);
        $payment->setMethod($paymentMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    /**
     * @param OrderInterface $order
     * @param ProductVariantInterface $variant
     * @param int $price
     */
    private function addVariantWithPriceToOrder(OrderInterface $order, ProductVariantInterface $variant, $price)
    {
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($variant);
        $item->setUnitPrice($price);

        $this->itemQuantityModifier->modify($item, 1);

        $order->addItem($item);
    }
}
