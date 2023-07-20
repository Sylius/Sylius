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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderShippingMethodEligibilityValidator extends ConstraintValidator
{
    public function __construct(private ShippingMethodEligibilityCheckerInterface $methodEligibilityChecker)
    {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var OrderInterface $value */
        Assert::isInstanceOf($value, OrderInterface::class);

        /** @var OrderShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodEligibility::class);

        $shipments = $value->getShipments();
        if ($shipments->isEmpty()) {
            return;
        }

        foreach ($shipments as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();

            if (!$shippingMethod->isEnabled() || !$shippingMethod->getChannels()->contains($value->getChannel())) {
                $this->context->addViolation(
                    $constraint->getMethodNotAvailableMessage(),
                    ['%shippingMethodName%' => $shippingMethod->getName()],
                );

                continue;
            }

            if (!$this->methodEligibilityChecker->isEligible($shipment, $shippingMethod)) {
                $this->context->addViolation(
                    $constraint->getMessage(),
                    ['%shippingMethodName%' => $shipment->getMethod()->getName()],
                );
            }
        }
    }
}
