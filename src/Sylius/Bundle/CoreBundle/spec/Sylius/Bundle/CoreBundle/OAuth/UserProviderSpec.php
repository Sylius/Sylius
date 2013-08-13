<?php

namespace spec\Sylius\Bundle\CoreBundle\OAuth;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UserProviderSpec extends ObjectBehavior
{
    /**
     * @param FOS\UserBundle\Model\UserManagerInterface $userManagerInterface
     */
    function let($userManagerInterface)
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
