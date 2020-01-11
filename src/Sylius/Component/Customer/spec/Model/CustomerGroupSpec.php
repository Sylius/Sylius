<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Customer\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

final class CustomerGroupSpec extends ObjectBehavior
{
    function it_implements_customer_group_interface(): void
    {
        $this->shouldImplement(CustomerGroupInterface::class);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Retail');
        $this->getName()->shouldReturn('Retail');
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('#001');
        $this->getCode()->shouldReturn('#001');
    }
}
