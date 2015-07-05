<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RefreshTokenSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\RefreshToken');
    }

    function it_is_a_refresh_token()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\RefreshToken');
    }
}
