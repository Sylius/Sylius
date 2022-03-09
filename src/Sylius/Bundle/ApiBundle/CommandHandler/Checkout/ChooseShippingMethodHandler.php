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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ChooseShippingMethodHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private ShipmentRepositoryInterface $shipmentRepository,
        private ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        private FactoryInterface $stateMachineFactory
    ) {
    }

    public function __invoke(ChooseShippingMethod $chooseShippingMethod): OrderInterface
    {
        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $chooseShippingMethod->orderTokenValue]);

        Assert::notNull($cart, 'Cart has not been found.');

        $stateMachine = $this->stateMachineFactory->get($cart, OrderCheckoutTransitions::GRAPH);

        Assert::true(
            $stateMachine->can(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING),
            'Order cannot have shipment method assigned.'
        );

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([
            'code' => $chooseShippingMethod->shippingMethodCode,
        ]);
        Assert::notNull($shippingMethod, 'Shipping method has not been found');

        $shipment = $this->shipmentRepository->findOneByOrderId($chooseShippingMethod->shipmentId, $cart->getId());
        Assert::notNull($shipment, 'Can not find shipment with given identifier.');

        Assert::true(
            $this->eligibilityChecker->isEligible($shipment, $shippingMethod),
            'Given shipment is not eligible for provided shipping method.'
        );

        $shipment->setMethod($shippingMethod);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

        return $cart;
    }
}
