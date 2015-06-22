<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AccessTokenSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\AccessToken');
    }

    function it_is_a_access_token()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\AccessToken');
    }
}
