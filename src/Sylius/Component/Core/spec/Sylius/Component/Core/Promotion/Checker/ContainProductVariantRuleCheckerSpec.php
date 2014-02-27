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

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariant;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Daniel Richter <nexyz9@gmail.com
 */
class ContainProductVariantRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ContainProductVariantRuleChecker');
    }

    function it_should_be_sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_subject_as_eligible_if_variant_is_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariant $variant
    )
    {
        $configuration = array('variant' => 1, 'only' => false, 'exclude' => false);

        $variant->getId()->willReturn(1);
        $item->getVariant()->willReturn($variant);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_should_recognize_subject_as_not_eligible_if_variant_is_not_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariant $variant
    )
    {
        $configuration = array('variant' => 1, 'only' => false, 'exclude' => false);

        $variant->getId()->willReturn(2);
        $item->getVariant()->willReturn($variant);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_limit_is_set_and_order_contain_more_variants(
        OrderInterface $subject,
        Collection $items
    )
    {
        $configuration = array('variant' => 1, 'only' => true, 'exclude' => false);

        $items->count()->willReturn(2);

        $subject->getItems()->willReturn($items);

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_exclude_is_set_and_order_contain_variant(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductVariant $variant
    )
    {
        $configuration = array('variant' => 1, 'only' => false, 'exclude' => true);

        $variant->getId()->willReturn(1);
        $item->getVariant()->willReturn($variant);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }
}
