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

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Webmozart\Assert\Assert;

final class OrderAddressModifier implements OrderAddressModifierInterface
{
    public function __construct(
        private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
        private AddressMapperInterface $addressMapper,
    ) {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function modify(
        OrderInterface $order,
        ?AddressInterface $billingAddress,
        ?AddressInterface $shippingAddress = null,
    ): OrderInterface {
        $stateMachine = $this->getStateMachine();
        Assert::true(
            $stateMachine->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS),
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

        $stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS);

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

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
