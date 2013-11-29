<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OAuth;

use FOS\UserBundle\Model\UserManagerInterface;
use PhpSpec\ObjectBehavior;

class UserProviderSpec extends ObjectBehavior
{
    function let(UserManagerInterface $userManagerInterface)
    {
        $this->beConstructedWith($userManagerInterface, array());
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OAuth\UserProvider');
    }

    function it_implements_Hwi_oauth_aware_user_provider_interface()
    {
        $this->shouldImplement('HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface');
    }
}
