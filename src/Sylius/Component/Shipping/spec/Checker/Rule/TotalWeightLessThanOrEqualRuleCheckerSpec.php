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

namespace spec\Sylius\Component\Shipping\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class TotalWeightLessThanOrEqualRuleCheckerSpec extends ObjectBehavior
{
    public function it_implements_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    public function it_recognizes_subject_if_total_weight_is_less_than_configured_weight(ShippingSubjectInterface $subject): void
    {
        $subject->getShippingWeight()->willReturn(4);

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(true);
    }

    public function it_recognizes_subject_if_total_weight_is_equal_to_configured_weight(ShippingSubjectInterface $subject): void
    {
        $subject->getShippingWeight()->willReturn(5);

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(true);
    }

    public function it_denies_subject_if_total_weight_is_greater_than_configured_weight(ShippingSubjectInterface $subject): void
    {
        $subject->getShippingWeight()->willReturn(6);

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(false);
    }
}
