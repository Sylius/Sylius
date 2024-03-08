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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderShippingMethodEligibilityValidator extends ConstraintValidator
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var OrderShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodEligibility::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();

            if (!$shippingMethod->isEnabled() || !$shippingMethod->getChannels()->contains($order->getChannel())) {
                $this->context->addViolation(
                    $constraint->getMethodNotAvailableMessage(),
                    ['%shippingMethodName%' => $shippingMethod->getName()],
                );

                continue;
            }

            if (!$this->eligibilityChecker->isEligible($shipment, $shippingMethod)) {
                $this->context->addViolation(
                    $constraint->getMessage(),
                    ['%shippingMethodName%' => $shippingMethod->getName()],
                );
            }
        }
    }
}
