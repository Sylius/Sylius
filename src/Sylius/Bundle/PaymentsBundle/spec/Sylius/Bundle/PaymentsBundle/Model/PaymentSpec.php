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
class PaymentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\Payment');
    }

    function it_implements_Sylius_payment_instruction_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\PaymentInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_payment_method_by_default()
    {
        $this->getMethod()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface $method
     */
    function its_method_is_mutable($method)
    {
      $this->setMethod($method);
      $this->getMethod()->shouldReturn($method);
    }

    function it_has_no_source_by_default()
    {
        $this->getSource()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface $source
     */
    function it_allows_to_assign_a_source($source)
    {
        $this->setSource($source);
        $this->getSource()->shouldReturn($source);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface $source
     */
    function it_allows_to_remove_a_source($source)
    {
        $this->setSource($source);
        $this->setSource(null);
        $this->getSource()->shouldReturn(null);
    }

    function it_has_no_currency_by_default()
    {
        $this->getCurrency()->shouldReturn(null);
    }

    function its_currency_is_mutable()
    {
        $this->setCurrency('EUR');
        $this->getCurrency()->shouldReturn('EUR');
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

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\TransactionInterface $transactionA
     * @param Sylius\Bundle\PaymentsBundle\Model\TransactionInterface $transactionB
     */
    function it_calculates_balance_by_subtracting_transacttions_total_from_amount($transactionA, $transactionB)
    {
        $transactionA->getAmount()->willReturn(5000);
        $transactionB->getAmount()->willReturn(2500);

        $transactionA->setPayment($this)->shouldBeCalled();
        $transactionB->setPayment($this)->shouldBeCalled();

        $this->addTransaction($transactionA);
        $this->addTransaction($transactionB);

        $this->setAmount(7500);
        $this->getBalance()->shouldReturn(0);

        $this->setAmount(10000);
        $this->getBalance()->shouldReturn(2500);
    }

    function it_initializes_transaction_collection_by_default()
    {
        $this->getTransactions()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\TransactionInterface $transaction
     */
    function it_adds_transactions($transaction)
    {
        $this->hasTransaction($transaction)->shouldReturn(false);

        $transaction->setPayment($this)->shouldBeCalled();
        $this->addTransaction($transaction);

        $this->hasTransaction($transaction)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\TransactionInterface $transaction
     */
    function it_removes_transactions($transaction)
    {
        $transaction->setPayment($this)->shouldBeCalled();
        $this->addTransaction($transaction);

        $transaction->setPayment(null)->shouldBeCalled();
        $this->removeTransaction($transaction);

        $this->hasTransaction($transaction)->shouldReturn(false);
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
