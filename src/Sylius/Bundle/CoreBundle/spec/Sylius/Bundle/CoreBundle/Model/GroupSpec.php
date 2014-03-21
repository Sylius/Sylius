<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class GroupSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Group');
    }

    function it_implements_Sylius_group_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Model\GroupInterface');
    }

    function it_extends_FOS_group_model()
    {
        $this->shouldHaveType('FOS\UserBundle\Model\Group');
    }
}
