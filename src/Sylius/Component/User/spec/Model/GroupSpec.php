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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\Group');
    }

    public function it_implements_Sylius_group_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Model\GroupInterface');
    }

    public function it_extends_FOS_group_model()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\Group');
    }
}
