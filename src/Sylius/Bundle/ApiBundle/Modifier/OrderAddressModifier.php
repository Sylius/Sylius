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

namespace Sylius\Bundle\ApiBundle\Modifier;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Webmozart\Assert\Assert;

final readonly class OrderAddressModifier implements OrderAddressModifierInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
        private AddressMapperInterface $addressMapper,
    ) {
    }

    public function modify(
        OrderInterface $order,
        ?AddressInterface $billingAddress,
        ?AddressInterface $shippingAddress = null,
    ): OrderInterface {
        Assert::true(
            $this->stateMachine->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS),
            sprintf('Order with %s token cannot be addressed.', $order->getTokenValue()),
        );

        $channel = $order->getChannel();
        Assert::notNull($channel);

        if ($channel->isShippingAddressInCheckoutRequired()) {
            Assert::notNull($shippingAddress);
            $billingAddress = $billingAddress ?? clone $shippingAddress;
        } else {
            Assert::notNull($billingAddress);
            $shippingAddress = $shippingAddress ?? clone $billingAddress;
        }

        /** @var AddressInterface|null $oldBillingAddress */
        $oldBillingAddress = $order->getBillingAddress();
        /** @var AddressInterface|null $oldShippingAddress */
        $oldShippingAddress = $order->getShippingAddress();

        $this->setBillingAddress($order, $oldBillingAddress, $billingAddress);
        $this->setShippingAddress($order, $oldShippingAddress, $shippingAddress);

        $this->stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        return $order;
    }

    private function setBillingAddress(
        OrderInterface $order,
        ?AddressInterface $oldBillingAddress,
        ?AddressInterface $billingAddress,
    ): void {
        if ($oldBillingAddress !== null) {
            $order->setBillingAddress($this->addressMapper->mapExisting($oldBillingAddress, $billingAddress));

            return;
        }

        $order->setBillingAddress($billingAddress);
    }

    private function setShippingAddress(
        OrderInterface $order,
        ?AddressInterface $oldShippingAddress,
        ?AddressInterface $shippingAddress,
    ): void {
        if ($oldShippingAddress !== null) {
            $order->setShippingAddress($this->addressMapper->mapExisting($oldShippingAddress, $shippingAddress));

            return;
        }

        $order->setShippingAddress($shippingAddress);
    }
}
