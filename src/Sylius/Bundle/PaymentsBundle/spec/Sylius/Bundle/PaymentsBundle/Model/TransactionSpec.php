<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TransactionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\Transaction');
    }

    function it_implements_Sylius_transaction_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\TransactionInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_a_payment_by_default()
    {
        $this->getPayment()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentInterface $payment
     */
    function it_allows_assigning_itself_to_a_payment($payment)
    {
      $this->setPayment($payment);
      $this->getPayment()->shouldReturn($payment);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentInterface $payment
     */
    function it_allows_detaching_itself_from_a_payment($payment)
    {
        $this->setPayment($payment);
        $this->setPayment(null);

        $this->getPayment()->shouldReturn(null);
    }

    function it_throws_exception_if_trying_to_get_currency_without_payment_defined()
    {
        $this
          ->shouldThrow('BadMethodCallException')
          ->duringGetCurrency()
        ;
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentInterface $payment
     */
    function it_gets_currency_via_payment($payment)
    {
        $payment->getCurrency()->willReturn('USD')->shouldBeCalled();
        $this->setPayment($payment);

        $this->getCurrency()->shouldReturn('USD');
    }

    function it_has_amount_equal_to_0_by_defualt()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable()
    {
        $this->setAmount(4999);
        $this->getAmount()->shouldReturn(4999);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
