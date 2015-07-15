<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;

class RefreshTokenSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\RefreshToken');
    }

    public function it_is_a_refresh_token()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\RefreshToken');
    }
}
