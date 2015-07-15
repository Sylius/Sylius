<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;

class AccessTokenSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\AccessToken');
    }

    public function it_is_a_access_token()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\AccessToken');
    }
}
