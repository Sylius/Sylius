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

namespace Sylius\Component\Promotion\Checker\Rule;

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

trigger_deprecation(
    'sylius/promotion-bundle',
    '1.13',
    'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
    ItemTotalRuleChecker::class,
    \Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker::class,
);

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see \Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker} instead. */
final class ItemTotalRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'item_total';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        return $subject->getPromotionSubjectTotal() >= $configuration['amount'];
    }
}
