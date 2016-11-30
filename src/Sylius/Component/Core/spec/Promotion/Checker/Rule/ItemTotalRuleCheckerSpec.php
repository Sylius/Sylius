<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Form\Type\Rule\ItemTotalConfigurationType;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ChannelBasedRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ItemTotalRuleCheckerSpec extends ObjectBehavior
{
    function let(RuleCheckerInterface $itemTotalRuleChecker)
    {
        $this->beConstructedWith($itemTotalRuleChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ItemTotalRuleChecker::class);
    }

    function it_is_be_a_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_implements_channel_aware_rule_checker_interface()
    {
        $this->shouldImplement(ChannelBasedRuleCheckerInterface::class);
    }

    function it_uses_decorated_checker_to_check_eligibility_for_order_channel(
        ChannelInterface $channel,
        OrderInterface $order,
        RuleCheckerInterface $itemTotalRuleChecker
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $itemTotalRuleChecker->isEligible($order, ['amount' => 1000])->willReturn(true);

        $this->isEligible($order, ['WEB_US' => ['amount' => 1000]])->shouldReturn(true);
    }

    function it_returns_false_if_there_is_no_configuration_for_order_channel(
        ChannelInterface $channel,
        OrderInterface $order
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB_US');

        $this->isEligible($order, [])->shouldReturn(false);
    }

    function it_throws_exception_if_passed_subject_is_not_order(PromotionSubjectInterface $promotionSubject)
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('isEligible', [$promotionSubject, []])
        ;
    }

    function it_returns_a_total_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn(ItemTotalConfigurationType::class);
    }
}
