<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentSpec extends ObjectBehavior
{
    function it_implements_sylius_payment_interface(): void
    {
        $this->shouldImplement(PaymentInterface::class);
    }

    function it_has_no_id_by_default(): void
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

    function it_has_no_currency_code_by_default(): void
    {
        $this->getCurrencyCode()->shouldReturn(null);
    }

    function its_currency_code_is_mutable(): void
    {
        $this->setCurrencyCode('EUR');
        $this->getCurrencyCode()->shouldReturn('EUR');
    }

    function it_has_amount_equal_to_0_by_default(): void
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_is_mutable(): void
    {
        $this->setAmount(4999);
        $this->getAmount()->shouldReturn(4999);
    }

    function it_has_cart_state_by_default(): void
    {
        $this->getState()->shouldReturn(PaymentInterface::STATE_CART);
    }

    function its_state_is_mutable(): void
    {
        $this->setState(PaymentInterface::STATE_COMPLETED);
        $this->getState()->shouldReturn(PaymentInterface::STATE_COMPLETED);
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

    function its_details_are_mutable(): void
    {
        $this->setDetails(['foo', 'bar']);
        $this->getDetails()->shouldReturn(['foo', 'bar']);
    }
}
