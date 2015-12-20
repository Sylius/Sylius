<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\GroupInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\Group');
    }

    function it_implements_Sylius_group_interface()
    {
        $this->shouldImplement(GroupInterface::class);
    }

    function it_sets_name()
    {
        $this->setName('testGroup');

        $this->getName()->shouldReturn('testGroup');
    }
}
