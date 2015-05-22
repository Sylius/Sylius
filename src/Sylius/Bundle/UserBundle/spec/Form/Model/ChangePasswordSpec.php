<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ChangePasswordSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\Model\ChangePassword');
    }

    function it_has_current_password()
    {
        $this->setCurrentPassword('testPassword');
        $this->getCurrentPassword()->shouldReturn('testPassword');
    }

    function it_has_new_password()
    {
        $this->setNewPassword('testPassword');
        $this->getNewPassword()->shouldReturn('testPassword');
    }
}
