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
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PasswordResetRequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PasswordResetRequest::class);
    }

    function it_has_email()
    {
        $this->setEmail('test@example.com');
        $this->getEmail()->shouldReturn('test@example.com');
    }
}
