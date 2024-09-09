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

namespace spec\Sylius\Component\Core\Promotion\Checker\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class ContainsProductRuleCheckerSpec extends ObjectBehavior
{
    function it_implements_a_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_throws_an_exception_if_the_promotion_subject_is_not_an_order(PromotionSubjectInterface $subject): void
    {
        $this
            ->shouldThrow(new UnexpectedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, []])
        ;
    }

    function it_returns_true_if_product_is_right(
        OrderInterface $subject,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductInterface $shaft,
        ProductInterface $head,
    ): void {
        $subject->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $firstOrderItem->getProduct()->willReturn($head);
        $secondOrderItem->getProduct()->willReturn($shaft);
        $head->getCode()->willReturn('LACROSSE_HEAD');
        $shaft->getCode()->willReturn('LACROSSE_SHAFT');

        $this->isEligible($subject, ['product_code' => 'LACROSSE_SHAFT'])->shouldReturn(true);
    }

    function it_returns_false_if_product_is_wrong(
        OrderInterface $subject,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        ProductInterface $shaft,
        ProductInterface $head,
    ): void {
        $subject->getItems()->willReturn(new ArrayCollection([$firstOrderItem->getWrappedObject(), $secondOrderItem->getWrappedObject()]));
        $firstOrderItem->getProduct()->willReturn($head);
        $secondOrderItem->getProduct()->willReturn($shaft);
        $head->getCode()->willReturn('LACROSSE_HEAD');
        $shaft->getCode()->willReturn('LACROSSE_SHAFT');

        $this->isEligible($subject, ['product_code' => 'LACROSSE_STRING'])->shouldReturn(false);
    }
}
