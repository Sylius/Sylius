<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ItemTotalRuleChecker implements RuleCheckerInterface, ChannelBasedRuleCheckerInterface
{
    /**
     * @var RuleCheckerInterface
     */
    private $itemTotalRuleChecker;

    /**
     * @param RuleCheckerInterface $itemTotalRuleChecker
     */
    public function __construct(RuleCheckerInterface $itemTotalRuleChecker)
    {
        $this->itemTotalRuleChecker = $itemTotalRuleChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        Assert::isInstanceOf($subject, OrderInterface::class);

        $channelCode = $subject->getChannel()->getCode();
        if (!isset($configuration[$channelCode])) {
            return false;
        }

        return $this->itemTotalRuleChecker->isEligible($subject, $configuration[$channelCode]);
    }
}
