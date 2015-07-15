<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;

class AuthCodeSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\AuthCode');
    }

    public function it_is_a_auth_code()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\AuthCode');
    }
}
