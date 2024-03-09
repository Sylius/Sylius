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

use Sylius\Bundle\CoreBundle\Message\ResendShipmentConfirmationEmail;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ResendShipmentConfirmationEmailWithValidShipmentStateValidator extends ConstraintValidator
{
    /**
     * @param RepositoryInterface<ShipmentInterface> $shipmentRepository
     */
    public function __construct(private RepositoryInterface $shipmentRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ResendShipmentConfirmationEmail) {
            throw new UnexpectedTypeException($value, ResendShipmentConfirmationEmail::class);
        }

        if (!$constraint instanceof ResendShipmentConfirmationEmailWithValidShipmentState) {
            throw new UnexpectedTypeException($constraint, ResendShipmentConfirmationEmailWithValidShipmentState::class);
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneBy(['id' => $value->getShipmentId()]);
        if (null === $shipment) {
            return;
        }

        if ($shipment->getState() !== ShipmentInterface::STATE_SHIPPED) {
            $this->context->addViolation(
                $constraint->message,
                ['%state%' => $shipment->getState()],
            );
        }
    }
}
