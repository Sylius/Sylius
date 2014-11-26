<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Checker\Registry;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\RuleInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ExistingRuleCheckerExceptionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(RuleInterface::TYPE_ITEM_TOTAL);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Checker\Registry\ExistingRuleCheckerException');
    }

    function it_extends_invalid_argument_exception()
    {
        $this->shouldHaveType('InvalidArgumentException');
    }
}
