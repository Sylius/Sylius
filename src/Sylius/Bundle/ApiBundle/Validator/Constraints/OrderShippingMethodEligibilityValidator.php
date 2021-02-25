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

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareCommandInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderShippingMethodEligibilityValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ShippingMethodEligibilityCheckerInterface */
    private $eligibilityChecker;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ) {
        $this->orderRepository = $orderRepository;
        $this->eligibilityChecker = $eligibilityChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, OrderTokenValueAwareCommandInterface::class);

        /** @var OrderShippingMethodEligibility $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodEligibility::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();

            if (!$this->eligibilityChecker->isEligible($shipment, $shippingMethod)) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%shippingMethodName%' => $shippingMethod->getName()]
                );
            }
        }
    }
}
