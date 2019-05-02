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

namespace Sylius\Component\Shipping\Checker\Rule;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Webmozart\Assert\Assert;

final class TotalWeightLessThanOrEqualRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_weight_less_than_or_equal';

    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        /** @var ShipmentInterface $subject */
        Assert::isInstanceOf($subject, ShipmentInterface::class);

        $order = $subject->getOrder();
        if (null === $order) {
            // if the order is null the weight is less than anything
            return true;
        }

        $totalWeight = 0;
        foreach ($order->getShipments() as $shipment) {
            $totalWeight += $shipment->getShippingWeight();
        }

        return $totalWeight <= $configuration['weight'];
    }
}
