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
use Sylius\Component\Core\Model\Product;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Daniel Richter <nexyz9@gmail.com
 */
class ContainProductRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\ContainProductRuleChecker');
    }

    function it_should_be_sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_subject_as_eligible_if_product_is_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        Product $product
    )
    {
        $configuration = array('product' => 1, 'only' => false, 'exclude' => false);

        $product->getId()->willReturn(1);
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_should_recognize_subject_as_not_eligible_if_product_is_not_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        Product $product
    )
    {
        $configuration = array('product' => 1, 'only' => false, 'exclude' => false);

        $product->getId()->willReturn(2);
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_limit_is_set_and_order_contain_more_products(
        OrderInterface $subject,
        Collection $items
    )
    {
        $configuration = array('product' => 1, 'only' => true, 'exclude' => false);

        $items->count()->willReturn(2);

        $subject->getItems()->willReturn($items);

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_should_recognize_subject_as_not_eligible_if_exclude_is_set_and_order_contain_variant(
        OrderInterface $subject,
        OrderItemInterface $item,
        Product $product
    )
    {
        $configuration = array('product' => 1, 'only' => false, 'exclude' => true);

        $product->getId()->willReturn(1);
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }
}
