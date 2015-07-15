<?php

namespace spec\Sylius\Bundle\ApiBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthCodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ApiBundle\Model\AuthCode');
    }

    function it_is_a_auth_code()
    {
        $this->shouldHaveType('FOS\OAuthServerBundle\Entity\AuthCode');
    }
}
