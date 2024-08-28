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

use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ChosenShippingMethodEligibilityValidator extends ConstraintValidator
{
    /**
     * @param ShipmentRepositoryInterface<ShipmentInterface> $shipmentRepository
     * @param ShippingMethodRepositoryInterface<ShippingMethodInterface> $shippingMethodRepository
     */
    public function __construct(
        private readonly ShipmentRepositoryInterface $shipmentRepository,
        private readonly ShippingMethodRepositoryInterface $shippingMethodRepository,
        private readonly ShippingMethodsResolverInterface $shippingMethodsResolver,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ChooseShippingMethod $value */
        Assert::isInstanceOf($value, ChooseShippingMethod::class);

        /** @var ChosenShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, ChosenShippingMethodEligibility::class);

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $value->getShippingMethodCode()]);
        if (null === $shippingMethod) {
            $this->context->addViolation($constraint->notFoundMessage, ['%code%' => $value->getShippingMethodCode()]);

            return;
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($value->getShipmentId());

        if (null === $shipment) {
            $this->context->addViolation($constraint->shipmentNotFoundMessage);

            return;
        }

        $order = $shipment->getOrder();

        if ($order->getShippingAddress() === null) {
            $this->context->addViolation($constraint->shippingAddressNotFoundMessage);
        }

        if (!in_array($shippingMethod, $this->shippingMethodsResolver->getSupportedMethods($shipment), true)) {
            $this->context->addViolation($constraint->message, ['%name%' => $shippingMethod->getName()]);
        }
    }
}
