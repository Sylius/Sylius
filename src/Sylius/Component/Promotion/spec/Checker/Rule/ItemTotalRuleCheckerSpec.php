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

namespace spec\Sylius\Component\Promotion\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleCheckerSpec extends ObjectBehavior
{
    function it_is_be_a_rule_checker(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_an_empty_subject_as_not_eligible(PromotionSubjectInterface $subject): void
    {
        $subject->getPromotionSubjectTotal()->willReturn(0);

        $this->isEligible($subject, ['amount' => 500])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_not_eligible_if_a_subject_total_is_less_then_configured(
        PromotionSubjectInterface $subject
    ): void {
        $subject->getPromotionSubjectTotal()->willReturn(400);

        $this->isEligible($subject, ['amount' => 500])->shouldReturn(false);
    }

    function it_recognizes_a_subject_as_eligible_if_a_subject_total_is_greater_then_configured(
        PromotionSubjectInterface $subject
    ): void {
        $subject->getPromotionSubjectTotal()->willReturn(600);

        $this->isEligible($subject, ['amount' => 500])->shouldReturn(true);
    }

    function it_recognizes_a_subject_as_eligible_if_a_subject_total_is_equal_with_configured(
        PromotionSubjectInterface $subject
    ): void {
        $subject->getPromotionSubjectTotal()->willReturn(500);

        $this->isEligible($subject, ['amount' => 500])->shouldReturn(true);
    }
}
