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
use Sylius\Bundle\UserBundle\Form\Model\PasswordReset;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PasswordResetSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PasswordReset::class);
    }

    function it_has_new_password()
    {
        $this->setPassword('testPassword');
        $this->getPassword()->shouldReturn('testPassword');
    }
}
