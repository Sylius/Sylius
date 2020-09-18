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

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderShippingMethodEligibilityValidator extends ConstraintValidator
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var ShippingMethodRepositoryInterface */
    private $shippingMethodRepository;

    /** @var ShippingMethodEligibilityCheckerInterface */
    private $eligibilityChecker;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        /** @var OrderShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodEligibility::class);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->findOneBy(['id' => $value->shipmentId]);

        Assert::notNull($shipment);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy(['code' => $value->shippingMethodCode]);

        Assert::notNull($shippingMethod);

        if (!$this->eligibilityChecker->isEligible($shipment, $shippingMethod)) {
            $this->context->addViolation(
                $constraint->message,
                ['%shippingMethodName%' => $shippingMethod->getName()]
            );
        }
    }
}
