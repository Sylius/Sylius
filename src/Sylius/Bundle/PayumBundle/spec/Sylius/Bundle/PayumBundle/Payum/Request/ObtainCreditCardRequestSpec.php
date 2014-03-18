<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\PayumBundle\Payum\Request;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Component\Payment\Model\CreditCardInterface;

class ObtainCreditCardRequestSpec extends ObjectBehavior
{
    function let(OrderInterface $order)
    {
        $this->beConstructedWith($order);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest');
    }

    function it_should_allow_get_order_set_in_constructor(OrderInterface $order)
    {
        $this->getOrder()->shouldReturn($order);
    }

    function it_should_allow_get_credit_card_set_before(CreditCardInterface $creditCard)
    {
        $this->setCreditCard($creditCard);

        $this->getCreditCard()->shouldReturn($creditCard);
    }
}
