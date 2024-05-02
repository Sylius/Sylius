<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\OrderPaymentTransitions;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    /**
     * @param FactoryInterface<OrderInterface> $orderFactory
     * @param FactoryInterface<AddressInterface> $addressFactory
     * @param FactoryInterface<CustomerInterface> $customerFactory
     * @param FactoryInterface<OrderItemInterface> $orderItemFactory
     * @param FactoryInterface<ShipmentInterface> $shipmentFactory
     * @param RepositoryInterface<CountryInterface> $countryRepository
     * @param CustomerRepositoryInterface<CustomerInterface> $customerRepository
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param PaymentMethodRepositoryInterface<PaymentMethodInterface> $paymentMethodRepository
     * @param ShippingMethodRepositoryInterface<ShippingMethodInterface> $shippingMethodRepository
     */
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly FactoryInterface $orderFactory,
        private readonly FactoryInterface $addressFactory,
        private readonly FactoryInterface $customerFactory,
        private readonly FactoryInterface $orderItemFactory,
        private readonly FactoryInterface $shipmentFactory,
        private readonly StateMachineInterface $stateMachine,
        private readonly RepositoryInterface $countryRepository,
        private readonly RepositoryInterface $customerRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
        private readonly ProductVariantResolverInterface $variantResolver,
        private readonly OrderItemQuantityModifierInterface $itemQuantityModifier,
        private readonly ObjectManager $objectManager,
        private readonly DateTimeProviderInterface $dateTimeProvider,
        private readonly RandomnessGeneratorInterface $randomnessGenerator,
    ) {
    }

    /**
     * @Given /^there is (?:a|another) (customer "[^"]+") that placed an order$/
     * @Given /^there is (?:a|another) (customer "[^"]+") that placed (an order "[^"]+")$/
     * @Given a customer :customer placed an order :orderNumber
     * @Given the customer :customer has already placed an order :orderNumber
     * @Given there is a customer :customer that placed an order :orderNumber in channel :channel
     * @Given /^(this customer) placed (another order "[^"]+")$/
     */
    public function thereIsCustomerThatPlacedOrder(
        CustomerInterface $customer,
        ?string $orderNumber = null,
        ?ChannelInterface $channel = null,
    ): void {
        $order = $this->createOrder($customer, $orderNumber, $channel);

        $this->sharedStorage->set('customer', $customer);
        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given there is a customer :customer that placed an order :orderNumber later
     */
    public function thereIsACustomerThatPlacedAnOrderLater(CustomerInterface $customer, string $orderNumber): void
    {
        sleep(1);
        $this->thereIsCustomerThatPlacedOrder($customer, $orderNumber);
    }

    /**
     * @Given /^there is a (customer "[^"]+") that placed order with ("[^"]+" product) to ("[^"]+" based billing address) with ("[^"]+" shipping method) and ("[^"]+" payment) method$/
     */
    public function thereIsACustomerThatPlacedOrderWithProductToBasedBillingAddressWithShippingMethodAndPaymentMethod(
        CustomerInterface $customer,
        ProductInterface $product,
        AddressInterface $address,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $this->placeOrder($product, $shippingMethod, $address, $paymentMethod, $customer, 1);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the guest customer placed order with ("[^"]+" product) for "([^"]+)" and ("[^"]+" based billing address) with ("[^"]+" shipping method) and ("[^"]+" payment)$/
     */
    public function theGuestCustomerPlacedOrderWithForAndBasedShippingAddress(
        ProductInterface $product,
        string $email,
        AddressInterface $address,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $customer = $this->createCustomer($email);

        $this->customerRepository->add($customer);

        $this->placeOrder($product, $shippingMethod, $address, $paymentMethod, $customer, 1);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the another guest customer placed order with ("[^"]+" product) for "([^"]+)" and ("[^"]+" based billing address) with ("[^"]+" shipping method) and ("[^"]+" payment)$/
     */
    public function theAnotherGuestCustomerPlacedOrderWithForAndBasedShippingAddress(
        ProductInterface $product,
        string $email,
        AddressInterface $address,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $customer = $this->createCustomer($email);

        $this->customerRepository->add($customer);

        $this->sharedStorage->set('customer', $customer);

        $this->placeOrder($product, $shippingMethod, $address, $paymentMethod, $customer, 2);
        $this->objectManager->flush();
    }

    /**
     * @Given a customer :customer added something to cart
     */
    public function customerStartedCheckout(CustomerInterface $customer): void
    {
        $cart = $this->createCart($customer);

        $this->sharedStorage->set('cart', $cart);

        $this->orderRepository->add($cart);
    }

    /**
     * @Given the customer :customer added :product product to the cart
     */
    public function theCustomerAddedProductToTheCart(CustomerInterface $customer, ProductInterface $product): void
    {
        $cart = $this->createCart($customer);
        $variant = $this->getProductVariant($product);

        $this->addProductVariantsToOrderWithChannelPrice(
            $cart,
            $this->sharedStorage->get('channel'),
            $variant,
            1,
        );

        $this->orderRepository->add($cart);

        $this->sharedStorage->set('cart', $cart);
    }

    /**
     * @Given /^(I) placed (an order "[^"]+")$/
     */
    public function iPlacedAnOrder(ShopUserInterface $user, string $orderNumber): void
    {
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();
        $order = $this->createOrder($customer, $orderNumber);

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^the customer ("[^"]+" addressed it to "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+"))$/
     * @Given /^I (addressed it to "[^"]+", "[^"]+", "[^"]+" "[^"]+" in the "[^"]+"(?:|, "[^"]+"))$/
     */
    public function theCustomerAddressedItTo(AddressInterface $address): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $order->setShippingAddress($address);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer changed shipping address' street to :street
     */
    public function theCustomerChangedShippingAddressStreetTo(string $street): void
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
    public function forTheBillingAddressOf(AddressInterface $address): void
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
    public function theCustomerAddressedItToWithIdenticalBillingAddress(AddressInterface $address): void
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
        PaymentMethodInterface $paymentMethod,
    ): void {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->checkoutUsing($order, $shippingMethod, $address, $paymentMethod);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) (to "[^"]+")$/
     */
    public function theCustomerChoseShippingTo(ShippingMethodInterface $shippingMethod, AddressInterface $address): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) with ("[^"]+" payment)$/
     * @Given /^I chose ("[^"]+" shipping method) with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingWithPayment(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->proceedSelectingShippingAndPaymentMethod($order, $shippingMethod, $paymentMethod);
        $this->completeCheckout($order);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method)$/
     */
    public function theCustomerChoseShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
        if (!$order->getPayments()->isEmpty()) {
            $this->stateMachine->apply($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer chose ("[^"]+" payment)$/
     */
    public function theCustomerChosePayment(PaymentMethodInterface $paymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        foreach ($order->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
        }

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product
     * @Given I bought a single :product
     */
    public function theCustomerBoughtSingleProduct(ProductInterface $product, ?ChannelInterface $channel = null): void
    {
        $variant = $this->getProductVariant($product);

        $this->addProductVariantToOrder($variant, 1, $channel);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought another :product with separate :shippingMethod shipment
     */
    public function theCustomerBoughtAnotherProductWithSeparateShipment(
        ProductInterface $product,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $variant = $this->getProductVariant($product);

        $this->addProductVariantToOrder($variant, 1);

        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();
        $shipment->setMethod($shippingMethod);
        $shipment->setOrder($order);
        $order->addShipment($shipment);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ((?:a|an) "[^"]+") and ((?:a|an) "[^"]+")$/
     * @Given /^I bought ((?:a|an) "[^"]+") and ((?:a|an) "[^"]+")$/
     */
    public function theCustomerBoughtProductAndProduct(ProductInterface $product, ProductInterface $secondProduct): void
    {
        $this->theCustomerBoughtSingleProduct($product);
        $this->theCustomerBoughtSingleProduct($secondProduct);
    }

    /**
     * @Given /^the customer bought (\d+) ("[^"]+" products)$/
     */
    public function theCustomerBoughtSeveralProducts(int $quantity, ProductInterface $product): void
    {
        $variant = $this->getProductVariant($product);

        $this->addProductVariantToOrder($variant, $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought ([^"]+) units of ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSeveralVariantsOfProduct(int $quantity, ProductVariantInterface $variant): void
    {
        $this->addProductVariantToOrder($variant, $quantity);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the customer bought a single ("[^"]+" variant of product "[^"]+")$/
     * @Given /^the customer also bought a ("[^"]+" variant of product "[^"]+")$/
     */
    public function theCustomerBoughtSingleProductVariant(ProductVariantInterface $productVariant): void
    {
        $this->addProductVariantToOrder($productVariant);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought a single :product using :coupon coupon
     * @Given I bought a single :product using :coupon coupon
     */
    public function theCustomerBoughtSingleUsing(ProductInterface $product, PromotionCouponInterface $coupon): void
    {
        $variant = $this->getProductVariant($product);

        $order = $this->addProductVariantToOrder($variant);
        $order->setPromotionCoupon($coupon);

        $this->objectManager->flush();
    }

    /**
     * @Given I used :coupon coupon
     */
    public function iUsedCoupon(PromotionCouponInterface $coupon): void
    {
        $order = $this->sharedStorage->get('order');
        $order->setPromotionCoupon($coupon);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(I) have already placed (\d+) orders choosing ("[^"]+" product), ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     */
    public function iHaveAlreadyPlacedOrderNthTimes(
        ShopUserInterface $user,
        int $numberOfOrders,
        ProductInterface $product,
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod,
    ): void {
        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();
        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $this->placeOrder($product, $shippingMethod, $address, $paymentMethod, $customer, $i);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given there is an :orderNumber order with :product product
     * @Given there is an :orderNumber order with :product product in this channel
     * @Given there is an :orderNumber order with :product product in :channel channel
     * @Given there is a :state :orderNumber order with :product product
     */
    public function thereIsAOrderWithProduct(
        string $orderNumber,
        ProductInterface $product,
        ?string $state = null,
        ?ChannelInterface $channel = null,
    ): void {
        $order = $this->createOrder($this->createOrProvideCustomer('amba@fatima.org'), $orderNumber, $channel);

        $this->sharedStorage->set('order', $order);

        $this->theCustomerBoughtSingleProduct($product, $channel);

        $this->createShippingPaymentMethodsAndAddress();

        if ($state !== null) {
            foreach ($this->getTargetPaymentTransitions($state) as $transition) {
                $this->applyPaymentTransitionOnOrder($order, $transition);
            }
        }

        $this->orderRepository->add($order);
    }

    /**
     * @Given there is an :orderNumber order with :product product ordered later
     */
    public function thereIsAnOrderWithProductOrderedLater(string $orderNumber, ProductInterface $product): void
    {
        sleep(1);
        $this->thereIsAOrderWithProduct($orderNumber, $product);
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") at "([^"]+)"$/
     *
     * @throws \Exception
     */
    public function thisCustomerHasPlacedAnOrderAtDate(CustomerInterface $customer, string $number, string $checkoutCompletedAt): void
    {
        $order = $this->createOrder($customer, $number);
        $order->setCheckoutCompletedAt(new \DateTime($checkoutCompletedAt));
        $order->setState(BaseOrderInterface::STATE_NEW);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") on a (channel "[^"]+")$/
     */
    public function thisCustomerHasPlacedAnOrderOnAChannel(CustomerInterface $customer, string $number, ChannelInterface $channel): void
    {
        $order = $this->createOrder($customer, $number, $channel);
        $order->setState(BaseOrderInterface::STATE_NEW);

        $this->orderRepository->add($order);
        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Given /^(this customer) has(?:| also) started checkout on a (channel "[^"]+")$/
     */
    public function thisCustomerHasStartedCheckoutOnAChannel(CustomerInterface $customer, ChannelInterface $channel): void
    {
        $order = $this->createOrder($customer, null, $channel);

        $this->orderRepository->add($order);
        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Given /^(customer "[^"]+"|this customer) has(?:| also) placed (\d+) orders on the ("[^"]+" channel) in each buying (\d+) ("[^"]+" products?)$/
     */
    public function thisCustomerPlacedOrdersOnChannelBuyingProducts(
        CustomerInterface $customer,
        int $orderCount,
        ChannelInterface $channel,
        int $productCount,
        ProductInterface $product,
    ): void {
        $this->createOrdersForCustomer($customer, $orderCount, $channel, $productCount, $product);
    }

    /**
     * @Given /^(customer "[^"]+"|this customer) has(?:| also) fulfilled (\d+) orders placed on the ("[^"]+" channel) in each buying (\d+) ("[^"]+" products?)$/
     */
    public function thisCustomerFulfilledOrdersPlacedOnChannelBuyingProducts(
        CustomerInterface $customer,
        int $orderCount,
        ChannelInterface $channel,
        int $productCount,
        ProductInterface $product,
    ): void {
        $this->createOrdersForCustomer($customer, $orderCount, $channel, $productCount, $product, true);
    }

    /**
     * @Given /^(\d+) new customers have added products to the cart for total of ("[^"]+")$/
     */
    public function customersHaveAddedProductsToTheCartForTotalOf(int $numberOfCustomers, int $total): void
    {
        $customers = $this->generateCustomers($numberOfCustomers);

        $sampleProductVariant = $this->sharedStorage->get('variant');

        for ($i = 0; $i < $numberOfCustomers; ++$i) {
            $order = $this->createCart($customers[random_int(0, $numberOfCustomers - 1)]);

            $price = $i === ($numberOfCustomers - 1) ? $total : random_int(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^a single customer has placed an order for total of ("[^"]+")$/
     */
    public function aSingleCustomerHasPlacedAnOrderForTotalOf(int $total): void
    {
        $this->createOrders(numberOfCustomers: 1, numberOfOrders: 1, total: $total);
    }

    /**
     * @Given /^(\d+) (?:|more )new customers have placed (\d+) orders for total of ("[^"]+")$/
     */
    public function customersHavePlacedOrdersForTotalOf(int $numberOfCustomers, int $numberOfOrders, int $total): void
    {
        $this->createOrders($numberOfCustomers, $numberOfOrders, $total);
    }

    /**
     * @Given /^(\d+) new customers have fulfilled (\d+) orders placed for total of ("[^"]+")$/
     */
    public function customersHaveFulfilledOrdersPlacedForTotalOf(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
    ): void {
        $this->createOrders($numberOfCustomers, $numberOfOrders, $total, true);
    }

    /**
     * @Given /^(\d+) (?:|more )new customers have placed (\d+) orders for total of ("[^"]+") mostly ("[^"]+" product)$/
     */
    public function customersHavePlacedOrdersForTotalOfMostlyProduct(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
        ProductInterface $product,
    ): void {
        $this->createOrdersWithProduct($numberOfCustomers, $numberOfOrders, $total, $product);
    }

    /**
     * @Given /^(\d+) (?:|more )new customers have fulfilled (\d+) orders placed for total of ("[^"]+") mostly ("[^"]+" product)$/
     */
    public function customersHaveFulfilledOrdersPlacedForTotalOfMostlyProduct(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
        ProductInterface $product,
    ): void {
        $this->createOrdersWithProduct($numberOfCustomers, $numberOfOrders, $total, $product, true);
    }

    /**
     * @Given /^(\d+) (?:|more )new customers have paid (\d+) orders placed for total of ("[^"]+")$/
     */
    public function moreCustomersHavePaidOrdersPlacedForTotalOf(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
    ): void {
        $this->createPaidOrders($numberOfCustomers, $numberOfOrders, $total);
    }

    /**
     * @Given /^(this customer) has(?:| also) placed (an order "[^"]+") buying a single ("[^"]+" product) for ("[^"]+") on the ("[^"]+" channel)$/
     */
    public function customerHasPlacedAnOrderBuyingASingleProductForOnTheChannel(
        CustomerInterface $customer,
        string $orderNumber,
        ProductInterface $product,
        int $price,
        ChannelInterface $channel,
    ): void {
        $order = $this->createOrder($customer, $orderNumber, $channel);
        $order->setState(BaseOrderInterface::STATE_NEW);

        $variant = $this->getProductVariant($product);

        $this->addVariantWithPriceToOrder($order, $variant, $price);

        $this->orderRepository->add($order);
        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Given /^(this order) is already paid$/
     * @Given the order :order is already paid
     */
    public function thisOrderIsAlreadyPaid(OrderInterface $order): void
    {
        $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) has been refunded$/
     * @Given the customer has refunded the order with number :order
     */
    public function thisOrderHasBeenRefunded(OrderInterface $order): void
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
    public function theCustomerCancelledThisOrder(OrderInterface $order): void
    {
        $this->stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL);

        $this->objectManager->flush();
    }

    /**
     * @Given /^I cancelled my last order$/
     */
    public function theCustomerCancelledMyLastOrder(): void
    {
        $order = $this->sharedStorage->get('order');
        $this->stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this order) has already been shipped$/
     * @Given the order :order is already shipped
     */
    public function thisOrderHasAlreadyBeenShipped(OrderInterface $order): void
    {
        $this->applyShipmentTransitionOnOrder($order, ShipmentTransitions::TRANSITION_SHIP);

        $this->objectManager->flush();
    }

    /**
     * @When the customer used coupon :coupon
     */
    public function theCustomerUsedCoupon(PromotionCouponInterface $coupon): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $order->setPromotionCoupon($coupon);

        $this->objectManager->flush();
    }

    /**
     * @Given the order :order has been placed in :localeCode locale
     */
    public function theOrderHasBeenPlacedInLocale(OrderInterface $order, string $localeCode): void
    {
        $order->setLocaleCode($localeCode);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer completed the order
     */
    public function theCustomerCompletedTheOrder(): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        $this->completeCheckout($order);

        $this->objectManager->flush();
    }

    /**
     * @Given the :product product's inventory has become tracked with :numberOfItems items
     */
    public function theProductSInventoryHasBecameTrackedWithItems(ProductInterface $product, int $numberOfItems): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $product->getVariants()->first();
        $productVariant->setTracked(true);
        $productVariant->setOnHand($numberOfItems);

        $this->objectManager->flush();
    }

    private function applyShipmentTransitionOnOrder(OrderInterface $order, string $transition): void
    {
        foreach ($order->getShipments() as $shipment) {
            $this->stateMachine->apply($shipment, ShipmentTransitions::GRAPH, $transition);
        }
    }

    private function applyPaymentTransitionOnOrder(OrderInterface $order, string $transition): void
    {
        foreach ($order->getPayments() as $payment) {
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
        }
    }

    private function applyTransitionOnOrderCheckout(OrderInterface $order, string $transition): void
    {
        $this->stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, $transition);
    }

    private function addProductVariantToOrder(
        ProductVariantInterface $productVariant,
        int $quantity = 1,
        ?ChannelInterface $channel = null,
    ): OrderInterface {
        $order = $this->sharedStorage->get('order');

        $this->addProductVariantsToOrderWithChannelPrice(
            $order,
            $channel ?? $this->sharedStorage->get('channel'),
            $productVariant,
            $quantity,
        );

        return $order;
    }

    private function addProductVariantsToOrderWithChannelPrice(
        OrderInterface $order,
        ChannelInterface $channel,
        ProductVariantInterface $productVariant,
        int $quantity = 1,
    ): void {
        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($productVariant);

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $productVariant->getChannelPricingForChannel($channel);
        $item->setUnitPrice($channelPricing->getPrice());

        $this->itemQuantityModifier->modify($item, $quantity);

        $order->addItem($item);
    }

    private function createOrder(
        CustomerInterface $customer,
        ?string $number = null,
        ?ChannelInterface $channel = null,
    ): OrderInterface {
        $order = $this->createCart($customer, $channel);
        $order->setTokenValue($this->generateToken());

        if (null !== $number) {
            $order->setNumber($number);
        }

        $order->completeCheckout();

        return $order;
    }

    private function createCart(CustomerInterface $customer, ?ChannelInterface $channel = null): OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setChannel($channel ?? $this->sharedStorage->get('channel'));
        $order->setLocaleCode($this->sharedStorage->get('locale')->getCode());
        $order->setCurrencyCode($order->getChannel()->getBaseCurrency()->getCode());

        return $order;
    }

    private function createCustomer(string $email): CustomerInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail($email);
        $customer->setFirstName('John');
        $customer->setLastName('Doe');

        return $customer;
    }

    private function createOrProvideCustomer(string $email): CustomerInterface
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $email]);

        return $customer ?? $this->createCustomer($email);
    }

    /**
     * @return CustomerInterface[]
     */
    private function generateCustomers(int $count): array
    {
        $customers = [];

        for ($i = 0; $i < $count; ++$i) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail(sprintf('john%s@doe.com', uniqid()));
            $customer->setFirstname('John');
            $customer->setLastname('Doe' . $i);

            $customer->setCreatedAt($this->dateTimeProvider->now());

            $customers[] = $customer;

            $this->customerRepository->add($customer);
        }

        return $customers;
    }

    private function checkoutUsing(
        OrderInterface $order,
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->proceedSelectingShippingAndPaymentMethod($order, $shippingMethod, $paymentMethod);
        $this->completeCheckout($order);
    }

    private function completeCheckout(OrderInterface $order): void
    {
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function createShippingPaymentMethodsAndAddress(): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCity('Wawa');
        $address->setCountryCode($this->countryRepository->findOneBy([])->getCode());
        $address->setFirstName('Jon');
        $address->setLastName('Doe');
        $address->setPostcode('000');
        $address->setStreet('Happy');

        $this->theCustomerAddressedItToWithIdenticalBillingAddress($address);

        $shippingMethod = $this->shippingMethodRepository->findOneBy([]);
        Assert::notNull($shippingMethod);

        $paymentMethod = $this->paymentMethodRepository->findOneBy([]);
        Assert::notNull($paymentMethod);

        $this->theCustomerChoseShippingWithPayment($shippingMethod, $paymentMethod);
    }

    private function proceedSelectingShippingAndPaymentMethod(
        OrderInterface $order,
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void {
        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
        }
        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        $payment = $order->getLastPayment(PaymentInterface::STATE_CART);
        $payment->setMethod($paymentMethod);

        $this->applyTransitionOnOrderCheckout($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }

    private function addVariantWithPriceToOrder(OrderInterface $order, ProductVariantInterface $variant, int $price): void
    {
        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($variant);
        $item->setUnitPrice($price);

        $this->itemQuantityModifier->modify($item, 1);

        $order->addItem($item);
    }

    private function createOrders(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
        bool $isFulfilled = false,
    ): void {
        $customers = $this->generateCustomers($numberOfCustomers);
        $sampleProductVariant = $this->sharedStorage->get('variant');

        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $order = $this->createOrder($customers[random_int(0, $numberOfCustomers - 1)], '#' . uniqid());
            $this->stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $price = $i === ($numberOfOrders - 1) ? $total : random_int(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            if ($isFulfilled) {
                $this->payOrder($order);
                $this->shipOrder($order);
            }

            $order->setCheckoutCompletedAt($this->dateTimeProvider->now());

            $this->objectManager->persist($order);
            $this->sharedStorage->set('order', $order);
        }

        $this->objectManager->flush();
    }

    private function createPaidOrders(int $numberOfCustomers, int $numberOfOrders, int $total): void
    {
        $customers = $this->generateCustomers($numberOfCustomers);
        $sampleProductVariant = $this->sharedStorage->get('variant');

        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $order = $this->createOrder($customers[random_int(0, $numberOfCustomers - 1)], '#' . uniqid());
            $this->stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $price = $i === ($numberOfOrders - 1) ? $total : random_int(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            $this->payOrder($order);

            $this->objectManager->persist($order);
            $this->sharedStorage->set('order', $order);
        }

        $this->objectManager->flush();
    }

    private function createOrdersWithProduct(
        int $numberOfCustomers,
        int $numberOfOrders,
        int $total,
        ProductInterface $product,
        bool $isFulfilled = false,
    ): void {
        $customers = $this->generateCustomers($numberOfCustomers);

        /** @var ProductVariantInterface $sampleProductVariant */
        $sampleProductVariant = $product->getVariants()->first();

        for ($i = 0; $i < $numberOfOrders; ++$i) {
            $order = $this->createOrder($customers[random_int(0, $numberOfCustomers - 1)], '#' . uniqid(), $product->getChannels()->first());
            $this->stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE);
            $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

            $price = $i === ($numberOfOrders - 1) ? $total : random_int(1, $total);
            $total -= $price;

            $this->addVariantWithPriceToOrder($order, $sampleProductVariant, $price);

            if ($isFulfilled) {
                $this->payOrder($order);
                $this->shipOrder($order);
            }

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
    }

    private function createOrdersForCustomer(
        CustomerInterface $customer,
        int $orderCount,
        ChannelInterface $channel,
        int $productCount,
        ProductInterface $product,
        bool $isFulfilled = false,
    ): void {
        $variant = $this->getProductVariant($product);

        for ($i = 0; $i < $orderCount; ++$i) {
            $order = $this->createOrder($customer, uniqid('#'), $channel);

            $this->addProductVariantsToOrderWithChannelPrice(
                $order,
                $channel,
                $variant,
                $productCount,
            );

            $order->setState($isFulfilled ? BaseOrderInterface::STATE_FULFILLED : BaseOrderInterface::STATE_NEW);

            $this->objectManager->persist($order);
        }

        $this->objectManager->flush();
    }

    /** @return array<array-key, string> */
    private function getTargetPaymentTransitions(string $state): array
    {
        $state = strtolower($state);

        $transitions = [
            'new' => [],
            'processing' => [PaymentTransitions::TRANSITION_PROCESS],
            'completed' => [PaymentTransitions::TRANSITION_COMPLETE],
            'cancelled' => [PaymentTransitions::TRANSITION_CANCEL],
            'failed' => [PaymentTransitions::TRANSITION_FAIL],
            'refunded' => [PaymentTransitions::TRANSITION_COMPLETE, PaymentTransitions::TRANSITION_REFUND],
        ];

        return $transitions[$state];
    }

    private function placeOrder(
        ProductInterface $product,
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod,
        CustomerInterface $customer,
        int $number,
    ): void {
        $variant = $this->getProductVariant($product);

        $channelPricing = $variant->getChannelPricingForChannel($this->sharedStorage->get('channel'));

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($variant);
        $item->setUnitPrice($channelPricing->getPrice());

        $this->itemQuantityModifier->modify($item, 1);

        $order = $this->createOrder($customer, '#00000' . $number);
        $order->addItem($item);

        $this->checkoutUsing($order, $shippingMethod, clone $address, $paymentMethod);
        $this->applyPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->persist($order);
        $this->sharedStorage->set('order', $order);
    }

    private function getProductVariant(ProductInterface $product): ProductVariantInterface
    {
        /** @var ProductVariantInterface|null $variant */
        $variant = $this->variantResolver->getVariant($product);

        if ($variant === null) {
            throw new \RuntimeException(sprintf('Product "%s" has no variant', $product->getCode()));
        }

        return $variant;
    }

    private function shipOrder(OrderInterface $order): void
    {
        $this->stateMachine->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_SHIP);
    }

    private function payOrder(OrderInterface $order): void
    {
        $this->stateMachine->apply($order, OrderPaymentTransitions::GRAPH, OrderPaymentTransitions::TRANSITION_PAY);
    }

    private function generateToken(): string
    {
        do {
            $token = $this->randomnessGenerator->generateUriSafeString(10);
        } while ($this->orderRepository->findOneBy(['tokenValue' => $token]) !== null);

        return $token;
    }
}
