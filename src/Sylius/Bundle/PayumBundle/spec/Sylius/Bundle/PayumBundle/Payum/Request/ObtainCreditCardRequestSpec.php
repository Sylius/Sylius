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

class ObtainCreditCardRequestSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function let($order)
    {
        $this->beConstructedWith($order);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Request\ObtainCreditCardRequest');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_should_allow_get_order_set_in_constructor($order)
    {
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface $creditCard
     */
    function it_should_allow_get_credit_card_set_before($creditCard)
    {
        $this->setCreditCard($creditCard);

        $this->getCreditCard()->shouldReturn($creditCard);
    }
}
