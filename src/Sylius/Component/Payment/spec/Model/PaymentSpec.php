<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PaymentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Payment::class);
    }

    function it_implements_sylius_payment_interface()
    {
        $this->shouldImplement(PaymentInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_payment_method_by_default()
    {
        $this->getMethod()->shouldReturn(null);
    }

    function its_payment_method_is_mutable(PaymentMethodInterface $method)
    {
        $this->setMethod($method);
        $this->getMethod()->shouldReturn($method);
    }

    function it_has_no_currency_code_by_default()
    {
        $this->getCurrencyCode()->shouldReturn(null);
    }

    function its_currency_code_is_mutable()
    {
        $this->setCurrencyCode('EUR');
        $this->getCurrencyCode()->shouldReturn('EUR');
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

    function its_amount_should_accept_only_integer()
    {
        $this->setAmount(4498);
        $this->getAmount()->shouldBeInteger();
        $this->shouldThrow('\InvalidArgumentException')->duringSetAmount(44.98 * 100);
        $this->shouldThrow('\InvalidArgumentException')->duringSetAmount('4498');
        $this->shouldThrow('\InvalidArgumentException')->duringSetAmount(round(44.98 * 100));
        $this->shouldThrow('\InvalidArgumentException')->duringSetAmount([4498]);
        $this->shouldThrow('\InvalidArgumentException')->duringSetAmount(new \stdClass());
    }

    function it_has_cart_state_by_default()
    {
        $this->getState()->shouldReturn(PaymentInterface::STATE_CART);
    }

    function its_state_is_mutable()
    {
        $this->setState(PaymentInterface::STATE_COMPLETED);
        $this->getState()->shouldReturn(PaymentInterface::STATE_COMPLETED);
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

    function its_details_are_mutable()
    {
        $this->setDetails(['foo', 'bar']);
        $this->getDetails()->shouldReturn(['foo', 'bar']);
    }

    function its_details_could_be_set_from_traversable()
    {
        $details = new \ArrayObject(['foo', 'bar']);

        $this->setDetails($details);
        $this->getDetails()->shouldReturn(['foo', 'bar']);
    }

    function it_throws_exception_if_details_given_are_neither_array_nor_traversable()
    {
        $this->shouldThrow('Sylius\Component\Resource\Exception\UnexpectedTypeException')
            ->duringSetDetails('invalidDetails');
    }
}
