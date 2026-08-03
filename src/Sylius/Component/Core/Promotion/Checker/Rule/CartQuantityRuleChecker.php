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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class CartQuantityRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'cart_quantity';

    public function __construct(private ?ComparisonOperatorMatcherInterface $comparisonOperatorMatcher = null)
    {
    }

    /** @param array<array-key, mixed> $configuration */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof CountablePromotionSubjectInterface) {
            return false;
        }

        $promotionSubjectCount = $subject->getPromotionSubjectCount();
        $count = $configuration['count'];
        $comparisonOperator = $configuration['comparison_operator'] ?? $this->getComparisonOperatorMatcher()->getDefaultComparisonOperator();

        return $this->getComparisonOperatorMatcher()->match($promotionSubjectCount, $count, $comparisonOperator);
    }

    private function getComparisonOperatorMatcher(): ComparisonOperatorMatcherInterface
    {
        return $this->comparisonOperatorMatcher ??= new ComparisonOperatorMatcher();
    }
}
