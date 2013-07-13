<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Checker\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NonExistingRuleCheckerExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(RuleInterface::TYPE_ITEM_TOTAL);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Checker\Registry\NonExistingRuleCheckerException');
    }

    function it_should_be_an_exception()
    {
        $this->shouldHaveType('Exception');
    }

    function it_should_be_a_invalid_argument_exception()
    {
        $this->shouldHaveType('InvalidArgumentException');
    }
}
