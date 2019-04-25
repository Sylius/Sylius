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

// todo should the name be TotalWeightLessThanOrEqualRuleChecker?
final class TotalWeightLessThanRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_weight_less_than';

    /**
     * {@inheritdoc}
     */
    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        // todo $subject->getShippingWeight() probably does not return total weight. Figure this out
        return $subject->getShippingWeight() <= $configuration['weight'];
    }
}
