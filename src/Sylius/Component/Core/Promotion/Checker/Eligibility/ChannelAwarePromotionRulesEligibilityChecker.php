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

namespace Sylius\Component\Core\Promotion\Checker\Eligibility;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class ChannelAwarePromotionRulesEligibilityChecker implements PromotionEligibilityCheckerInterface, ChannelAwareConfigurationInterface
{
    public function __construct(private ServiceRegistryInterface $ruleRegistry)
    {
    }

    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion): bool
    {
        if (!$promotion->hasRules()) {
            return true;
        }

        foreach ($promotion->getRules() as $rule) {
            if ($this->isRuleSkippedForChannel($rule, $promotionSubject)) {
                continue;
            }

            if (!$this->isEligibleToRule($promotionSubject, $rule)) {
                return false;
            }
        }

        return true;
    }

    private function isRuleSkippedForChannel(PromotionRuleInterface $rule, PromotionSubjectInterface $subject): bool
    {
        $excludedChannels = $rule->getConfiguration()[self::EXCLUDED_CHANNELS_CONFIGURATION_KEY] ?? [];
        if ($excludedChannels === []) {
            return false;
        }

        if (!$subject instanceof OrderInterface) {
            return false;
        }

        return in_array($subject->getChannel()->getCode(), $excludedChannels, true);
    }

    private function isEligibleToRule(PromotionSubjectInterface $subject, PromotionRuleInterface $rule): bool
    {
        /** @var RuleCheckerInterface $checker */
        $checker = $this->ruleRegistry->get($rule->getType());

        $configuration = $rule->getConfiguration();
        unset($configuration[self::EXCLUDED_CHANNELS_CONFIGURATION_KEY]);

        return $checker->isEligible($subject, $configuration);
    }
}
