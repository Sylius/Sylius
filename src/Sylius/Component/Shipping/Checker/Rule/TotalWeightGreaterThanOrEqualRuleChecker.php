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

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class TotalWeightGreaterThanOrEqualRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_weight_greater_than_or_equal';

    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        return $subject->getShippingWeight() >= $configuration['weight'];
    }
}
