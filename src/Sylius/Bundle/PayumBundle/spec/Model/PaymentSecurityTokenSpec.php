<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Model;

use PhpSpec\ObjectBehavior;

class PaymentSecurityTokenSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Model\PaymentSecurityToken');
    }

    function it_extends_payum_token()
    {
        $this->shouldHaveType('Payum\Core\Model\Token');
    }
}
