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
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PaymentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\Payment');
    }

    function it_implements_Sylius_payment_interface()
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
    function its_payment_method_is_mutable($method)
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

    function it_has_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable()
    {
        $this->setAmount(4999);
        $this->getAmount()->shouldReturn(4999);
    }

    function it_has_new_state_by_default()
    {
        $this->getState()->shouldReturn(PaymentInterface::STATE_NEW);
    }

    function its_state_is_mutable()
    {
        $this->setState(PaymentInterface::STATE_COMPLETED);
        $this->getState()->shouldReturn(PaymentInterface::STATE_COMPLETED);
    }

    function it_initializes_log_collection_by_default()
    {
        $this->getLogs()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentLogInterface $log
     */
    function it_adds_logs($log)
    {
        $this->hasLog($log)->shouldReturn(false);
        $log->setPayment($this)->shouldBeCalled();
        $this->addLog($log);
        $this->hasLog($log)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentLogInterface $log
     */
    function it_removes_logs($log)
    {
        $this->addLog($log);
        $log->setPayment(null)->shouldBeCalled();
        $this->removeLog($log);
        $this->hasLog($log)->shouldReturn(false);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
