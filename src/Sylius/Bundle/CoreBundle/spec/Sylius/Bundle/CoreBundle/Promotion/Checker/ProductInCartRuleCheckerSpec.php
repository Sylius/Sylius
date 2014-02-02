<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Promotion\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\CoreBundle\Model\Product;

/**
 * @author Daniel Richter <nexyz9@gmail.com
 */
class ProductInCartRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Checker\ProductInCartRuleChecker');
    }

    function it_should_be_Sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_subject_as_eligible_if_product_is_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        Product $product
    )
    {
        $product->getId()->shouldBeCalled()->willReturn(1);
        $item->getProduct()->shouldBeCalled()->willReturn($product);
        $subject->getItems()->shouldBeCalled()->willReturn(array($item));

        $this->isEligible($subject, array('product' => 1))->shouldReturn(true);
    }

    function it_should_recognize_subject_as_not_eligible_if_product_is_not_associated(
        OrderInterface $subject,
        OrderItemInterface $item,
        Product $product
    )
    {
        $product->getId()->shouldBeCalled()->willReturn(2);
        $item->getProduct()->shouldBeCalled()->willReturn($product);
        $subject->getItems()->shouldBeCalled()->willReturn(array($item));

        $this->isEligible($subject, array('product' => 1))->shouldReturn(false);
    }
}
