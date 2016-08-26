<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Promotion\Checker\ContainsProductRuleChecker;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Piotr Walków <walkow.piotr@gmail.com>
 */
final class ContainsProductRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContainsProductRuleChecker::class);
    }

    function it_is_sylius_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_throws_exception_on_invalid_subject(PromotionSubjectInterface $subject)
    {
        $this->shouldThrow(UnsupportedTypeException::class)->duringIsEligible($subject, []);
    }

    function it_returns_true_if_variant_is_right_and_exclude_is_not_set(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(1);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => false])->shouldReturn(true);
    }

    function it_returns_false_if_variant_is_right_and_exclude_is_set(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(1);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => true])->shouldReturn(false);
    }

    function it_returns_true_if_variant_is_not_included_and_exclude_is_not_set(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(2);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => true])->shouldReturn(true);
    }

    function it_returns_false_if_variant_is_not_included_and_exclude_is_not_set(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(2);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => false])->shouldReturn(false);
    }

    function it_returns_true_if_variant_is_included_and_count_is_set_smaller_amount_than_quantity(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(1);

        $orderItem->getQuantity()->willReturn(10);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => false, 'count' => 2])->shouldReturn(true);
    }

    function it_returns_false_if_variant_is_included_and_count_is_set_bigger_amount_than_quantity(
        OrderInterface $subject,
        OrderItem $orderItem,
        ProductVariant $variant
    ) {
        $subject->getItems()->willReturn([$orderItem]);
        $orderItem->getVariant()->willReturn($variant);
        $variant->getId()->willReturn(1);

        $orderItem->getQuantity()->willReturn(1);

        $this->isEligible($subject, ['variant' => 1, 'exclude' => false, 'count' => 2])->shouldReturn(false);
    }
}
