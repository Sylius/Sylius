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
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleChecker implements RuleCheckerInterface
{
    public function __construct(private ?RuleCheckerInterface $itemTotalRuleChecker = null)
    {
        if ($this->itemTotalRuleChecker instanceof ItemTotalRuleChecker) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.13',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in 2.0.',
                ItemTotalRuleChecker::class,
                self::class,
            );
        }
    }

    /**
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

        return $subject->getPromotionSubjectTotal() >= $configuration[$channelCode]['amount'];
    }
}
