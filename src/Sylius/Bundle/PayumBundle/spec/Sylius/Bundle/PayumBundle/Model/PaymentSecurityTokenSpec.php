<?php

namespace spec\Sylius\Bundle\PayumBundle\Model;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PaymentSecurityTokenSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Model\PaymentSecurityToken');
    }

    function it_extends_payum_token()
    {
        $this->shouldHaveType('Payum\Model\Token');
    }
}
