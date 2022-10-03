<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Faker\Factory;
use Faker\Generator;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Webmozart\Assert\Assert;

final class OrderUpdater implements OrderUpdaterInterface
{
    private Generator $faker;

    public function __construct(
        private FactoryInterface $orderItemFactory,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private ProductRepositoryInterface $productRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private StateMachineFactoryInterface $stateMachineFactory,
        private FactoryInterface $addressFactory,
    ) {
        $this->faker = Factory::create();
    }

    public function update(OrderInterface $order, array $attributes): void
    {
        /** @var ChannelInterface $channel */
        $channel = $attributes['channel'];

        /** @var CountryInterface $country */
        $country = $attributes['country'];

        /** @var \DateTimeInterface $createdAt */
        $createdAt = $attributes['complete_date'];

        $currencyCode = $channel->getBaseCurrency()->getCode();
        $localeCode = $this->faker->randomElement($channel->getLocales()->toArray())->getCode();

        $order->setChannel($channel);
        $order->setCustomer($attributes['customer']);
        $order->setCurrencyCode($currencyCode);
        $order->setLocaleCode($localeCode);

        // $this->generateItems($order);

        $this->address($order, $country->getCode());
        // $this->selectShipping($order, $createdAt);
        $this->selectPayment($order, $createdAt);
        $this->completeCheckout($order);

        if ($attributes['fulfilled']) {
            $this->fulfillOrder($order);
        }
    }

    private function generateItems(OrderInterface $order): void
    {
        $numberOfItems = random_int(1, 5);
        $channel = $order->getChannel();
        $products = $this->productRepository->findLatestByChannel($channel, $order->getLocaleCode(), 100);
        if (0 === count($products)) {
            throw new \InvalidArgumentException(sprintf(
                'You have no enabled products at the channel "%s", but they are required to create an orders for that channel',
                $channel->getCode(),
            ));
        }

        $generatedItems = [];

        for ($i = 0; $i < $numberOfItems; ++$i) {
            /** @var ProductInterface $product */
            $product = $this->faker->randomElement($products);
            $variant = $this->faker->randomElement($product->getVariants()->toArray());

            if (array_key_exists($variant->getCode(), $generatedItems)) {
                /** @var OrderItemInterface $item */
                $item = $generatedItems[$variant->getCode()];
                $this->orderItemQuantityModifier->modify($item, $item->getQuantity() + random_int(1, 5));

                continue;
            }

            /** @var OrderItemInterface $item */
            $item = $this->orderItemFactory->createNew();

            $item->setVariant($variant);
            $this->orderItemQuantityModifier->modify($item, random_int(1, 5));

            $generatedItems[$variant->getCode()] = $item;
            $order->addItem($item);
        }
    }

    private function address(OrderInterface $order, string $countryCode): void
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setFirstName($this->faker->firstName);
        $address->setLastName($this->faker->lastName);
        $address->setStreet($this->faker->streetAddress);
        $address->setCountryCode($countryCode);
        $address->setCity($this->faker->city);
        $address->setPostcode($this->faker->postcode);

        $order->setShippingAddress($address);
        $order->setBillingAddress(clone $address);

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_ADDRESS);
    }

    private function selectShipping(OrderInterface $order, \DateTimeInterface $createdAt): void
    {
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_SHIPPING_SKIPPED) {
            return;
        }

        $channel = $order->getChannel();
        $shippingMethods = $this->shippingMethodRepository->findEnabledForChannel($channel);

        if (count($shippingMethods) === 0) {
            throw new \InvalidArgumentException(sprintf(
                'You have no shipping method available for the channel with code "%s", but they are required to proceed an order',
                $channel->getCode(),
            ));
        }

        $shippingMethod = $this->faker->randomElement($shippingMethods);

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        Assert::notNull($shippingMethod, $this->generateInvalidSkipMessage('shipping', $channel->getCode()));

        foreach ($order->getShipments() as $shipment) {
            $shipment->setMethod($shippingMethod);
            $shipment->setCreatedAt($createdAt);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
    }

    private function selectPayment(OrderInterface $order, \DateTimeInterface $createdAt): void
    {
        if ($order->getCheckoutState() === OrderCheckoutStates::STATE_PAYMENT_SKIPPED) {
            return;
        }

        $paymentMethod = $this
            ->faker
            ->randomElement($this->paymentMethodRepository->findEnabledForChannel($order->getChannel()))
        ;

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();
        Assert::notNull($paymentMethod, $this->generateInvalidSkipMessage('payment', $channel->getCode()));

        foreach ($order->getPayments() as $payment) {
            $payment->setMethod($paymentMethod);
            $payment->setCreatedAt($createdAt);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
    }

    private function applyCheckoutStateTransition(OrderInterface $order, string $transition): void
    {
        $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->apply($transition);
    }

    private function generateInvalidSkipMessage(string $type, string $channelCode): string
    {
        return sprintf(
            "No enabled %s method was found for the channel '%s'. " .
            "Set 'skipping_%s_step_allowed' option to true for this channel if you want to skip %s method selection.",
            $type,
            $channelCode,
            $type,
            $type,
        );
    }

    private function completeCheckout(OrderInterface $order): void
    {
        if ($this->faker->boolean(25)) {
            $order->setNotes($this->faker->sentence);
        }

        $this->applyCheckoutStateTransition($order, OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }

    private function fulfillOrder(OrderInterface $order): void
    {
        $this->completePayments($order);
        $this->completeShipments($order);
    }

    private function completePayments(OrderInterface $order): void
    {
        foreach ($order->getPayments() as $payment) {
            $stateMachine = $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH);
            if ($stateMachine->can(PaymentTransitions::TRANSITION_COMPLETE)) {
                $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE);
            }
        }
    }

    private function completeShipments(OrderInterface $order): void
    {
        foreach ($order->getShipments() as $shipment) {
            $stateMachine = $this->stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH);
            if ($stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)) {
                $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP);
            }
        }
    }
}
