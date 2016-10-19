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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @mixin ContainsProductRuleChecker
 *
 * @author Piotr Walków <walkow.piotr@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ContainsProductRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ContainsProductRuleChecker::class);
    }

    function it_implements_a_rule_checker_interface()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_throws_an_exception_if_the_promotion_subject_is_not_an_order(PromotionSubjectInterface $subject)
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
        ProductInterface $head
    ) {
        $subject->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
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
        ProductInterface $head
    ) {
        $subject->getItems()->willReturn([$firstOrderItem, $secondOrderItem]);
        $firstOrderItem->getProduct()->willReturn($head);
        $secondOrderItem->getProduct()->willReturn($shaft);
        $head->getCode()->willReturn('LACROSSE_HEAD');
        $shaft->getCode()->willReturn('LACROSSE_SHAFT');

        $this->isEligible($subject, ['product_code' => 'LACROSSE_STRING'])->shouldReturn(false);
    }
}
