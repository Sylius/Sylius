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
use Sylius\Component\User\Model\UserOAuth;
use Sylius\Component\User\Model\UserOAuthInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class UserOAuthSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UserOAuth::class);
    }

    function it_implements_user_oauth_interface()
    {
        $this->shouldImplement(UserOAuthInterface::class);
    }
}
