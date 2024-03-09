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

use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ShipmentAlreadyShippedValidator extends ConstraintValidator
{
    public function __construct(private ShipmentRepositoryInterface $shipmentRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, ShipmentAlreadyShipped::class);
        Assert::isInstanceOf($value, ShipShipment::class);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($value->shipmentId);

        if ($shipment->getState() === OrderShippingStates::STATE_SHIPPED) {
            $this->context->addViolation($constraint->message);
        }
    }
}
