<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Customer\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CustomerGroupSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerGroup::class);
    }

    function it_implements_customer_group_interface()
    {
        $this->shouldImplement(CustomerGroupInterface::class);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Retail');
        $this->getName()->shouldReturn('Retail');
    }

    function its_code_is_mutable()
    {
        $this->setCode('#001');
        $this->getCode()->shouldReturn('#001');
    }
}
