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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

final class ShopUserSpec extends ObjectBehavior
{
    function it_implements_user_component_interface(): void
    {
        $this->shouldImplement(ShopUserInterface::class);
    }

    function it_returns_customer_email(CustomerInterface $customer): void
    {
        $customer->getEmail()->willReturn('jon@snow.wall');
        $customer->setUser($this)->shouldBeCalled();

        $this->setCustomer($customer);

        $this->getEmail()->shouldReturn('jon@snow.wall');
    }

    function it_returns_null_as_customer_email_if_no_customer_is_assigned(): void
    {
        $this->getEmail()->shouldReturn(null);
    }

    function it_sets_customer_email(CustomerInterface $customer): void
    {
        $customer->setEmail('jon@snow.wall')->shouldBeCalled();
        $customer->setUser($this)->shouldBeCalled();

        $this->setCustomer($customer);

        $this->setEmail('jon@snow.wall');
    }

    function it_throws_an_exception_if_trying_to_set_email_while_no_customer_is_assigned(): void
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('setEmail', ['jon@snow.wall']);
    }

    function it_returns_customer_email_canonical(CustomerInterface $customer): void
    {
        $customer->getEmailCanonical()->willReturn('jon@snow.wall');
        $customer->setUser($this)->shouldBeCalled();

        $this->setCustomer($customer);

        $this->getEmailCanonical()->shouldReturn('jon@snow.wall');
    }

    function it_returns_null_as_customer_email_canonical_if_no_customer_is_assigned(): void
    {
        $this->getEmailCanonical()->shouldReturn(null);
    }

    function it_sets_customer_email_canonical(CustomerInterface $customer): void
    {
        $customer->setEmailCanonical('jon@snow.wall')->shouldBeCalled();
        $customer->setUser($this)->shouldBeCalled();

        $this->setCustomer($customer);

        $this->setEmailCanonical('jon@snow.wall');
    }

    function it_throws_an_exception_if_trying_to_set_email_canonical_while_no_customer_is_assigned(): void
    {
        $this->shouldThrow(UnexpectedTypeException::class)->during('setEmailCanonical', ['jon@snow.wall']);
    }
}
