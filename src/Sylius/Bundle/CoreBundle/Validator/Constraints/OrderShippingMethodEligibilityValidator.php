<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Shipping\Checker\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderShippingMethodEligibilityValidator extends ConstraintValidator
{
    /**
     * @var ShippingMethodEligibilityCheckerInterface
     */
    private $methodEligibilityChecker;

    /**
     * @param ShippingMethodEligibilityCheckerInterface $methodEligibilityChecker
     */
    public function __construct(ShippingMethodEligibilityCheckerInterface $methodEligibilityChecker)
    {
        $this->methodEligibilityChecker = $methodEligibilityChecker;
    }

    /**
     * @param OrderInterface $value
     *
     * {@inheritdoc}
     */
    public function validate($order, Constraint $constraint)
    {
        Assert::isInstanceOf($order, OrderInterface::class);

        $shipments = $order->getShipments();
        if ($shipments->isEmpty()) {
            return;
        }

        foreach ($shipments as $shipment) {
            if (!$this->methodEligibilityChecker->isEligible($shipment, $shipment->getMethod())) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%shippingMethodName%' => $shipment->getMethod()->getName()]
                );
            }
        }
    }
}
