<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;
use stdClass;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentRequestSpec extends ObjectBehavior
{
    function it_implements_sylius_payment_request_interface(): void
    {
        $this->shouldImplement(PaymentRequestInterface::class);
    }

    function it_has_no_hash_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_payment_method_by_default(): void
    {
        $this->getMethod()->shouldReturn(null);
    }

    function its_payment_method_is_mutable(PaymentMethodInterface $method): void
    {
        $this->setMethod($method);
        $this->getMethod()->shouldReturn($method);
    }

    function it_has_no_payment_by_default(): void
    {
        $this->getPayment()->shouldReturn(null);
    }

    function its_payment_is_mutable(PaymentInterface $payment): void
    {
        $this->setPayment($payment);
        $this->getPayment()->shouldReturn($payment);
    }

    function it_has_new_state_by_default(): void
    {
        $this->getState()->shouldReturn(PaymentRequestInterface::STATE_NEW);
    }

    function its_state_is_mutable(): void
    {
        $this->setState('test_state');
        $this->getState()->shouldReturn('test_state');
    }

    function it_has_capture_type_by_default(): void
    {
        $this->getType()->shouldReturn(PaymentRequestInterface::DATA_TYPE_CAPTURE);
    }

    function its_type_is_mutable(): void
    {
        $this->setType('test_type');
        $this->getType()->shouldReturn('test_type');
    }

    function it_has_null_data_by_default(): void
    {
        $this->getData()->shouldReturn(null);
    }

    function its_data_is_mutable(): void
    {
        $stdClass = new stdClass();
        $this->setData($stdClass);
        $this->getData()->shouldReturn($stdClass);
    }

    function it_has_empty_array_details_by_default(): void
    {
        $this->getDetails()->shouldReturn([]);
    }

    function its_details_are_mutable(): void
    {
        $this->setDetails(['foo', 'bar']);
        $this->getDetails()->shouldReturn(['foo', 'bar']);
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable(): void
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable(): void
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
