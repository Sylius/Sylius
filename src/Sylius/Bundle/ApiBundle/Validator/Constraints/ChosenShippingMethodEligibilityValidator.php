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

/** @experimental */
final class ChosenShippingMethodEligibilityValidator extends ConstraintValidator
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var ShippingMethodsResolverInterface */
    private $shippingMethodsResolver;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var ChooseShippingMethod $value */
        Assert::isInstanceOf($value, ChooseShippingMethod::class);

        /** @var ChosenShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, ChosenShippingMethodEligibility::class);

        /** @var ShippingMethodInterface|null $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $value->shippingMethodCode]);
        if (null === $shippingMethod) {
            $this->context->addViolation($constraint->notFoundMessage, ['%code%' => $value->shippingMethodCode]);

            return;
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($value->shipmentId);
        Assert::notNull($shipment);

        $order = $shipment->getOrder();

        if ($order->getShippingAddress() === null) {
            $this->context->addViolation($constraint->shippingAddressNotFoundMessage);
        }

        if (!in_array($shippingMethod, $this->shippingMethodsResolver->getSupportedMethods($shipment), true)) {
            $this->context->addViolation($constraint->message, ['%name%' => $shippingMethod->getName()]);
        }
    }
}
