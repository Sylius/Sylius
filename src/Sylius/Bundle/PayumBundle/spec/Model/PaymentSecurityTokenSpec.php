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
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Model\PaymentSecurityToken');
    }

    public function it_implements_payum_token_interface()
    {
        $this->shouldHaveType('Payum\Core\Security\TokenInterface');
    }
}
