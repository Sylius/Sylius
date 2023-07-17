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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderShippingMethodAvailableValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var OrderShippingMethodAvailable $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodAvailable::class);
        /** @var OrderInterface $value */
        Assert::isInstanceOf($value, OrderInterface::class);

        foreach ($value->getShipments() as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();

            if ($shippingMethod->isEnabled() && $shippingMethod->getChannels()->contains($value->getChannel())) {
                continue;
            }

            $this->context->addViolation(
                $constraint->message,
                ['%shippingMethodName%' => $shippingMethod->getName()],
            );
        }
    }
}
