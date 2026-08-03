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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcher;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'item_total';

    public function __construct(private ?ComparisonOperatorMatcherInterface $comparisonOperatorMatcher = null)
    {
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @throws UnsupportedTypeException
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        $channelConfig = $configuration[$channelCode];
        $promotionSubjectTotal = $subject->getPromotionSubjectTotal();
        $amount = $channelConfig['amount'];
        $comparisonOperator = $channelConfig['comparison_operator'] ?? $this->getComparisonOperatorMatcher()->getDefaultComparisonOperator();

        return $this->getComparisonOperatorMatcher()->match($promotionSubjectTotal, $amount, $comparisonOperator);
    }

    private function getComparisonOperatorMatcher(): ComparisonOperatorMatcherInterface
    {
        return $this->comparisonOperatorMatcher ??= new ComparisonOperatorMatcher();
    }
}
