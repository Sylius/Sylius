<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Generator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InstructionSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Generator\Instruction');
    }

    public function it_should_have_amount_equal_to_5_by_default()
    {
        $this->getAmount()->shouldReturn(5);
    }

    public function its_amount_should_be_mutable()
    {
        $this->setAmount(500);
        $this->getAmount()->shouldReturn(500);
    }

    public function it_should_not_have_usage_limit_by_default()
    {
        $this->getUsageLimit()->shouldReturn(null);
    }

    public function its_usage_limit_should_be_mutable()
    {
        $this->setUsageLimit(3);
        $this->getUsageLimit()->shouldReturn(3);
    }
}
