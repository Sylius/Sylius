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

namespace spec\Sylius\Bundle\CoreBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerResolverSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $customerFactory,
        CustomerProviderInterface $customerProvider,
    ): void {
        $this->beConstructedWith($customerFactory, $customerProvider);
    }

    function it_is_a_customer_provider(): void
    {
        $this->shouldImplement(CustomerResolverInterface::class);
    }

    function it_creates_a_customer_if_there_is_no_existing_one_with_given_email(
        FactoryInterface $customerFactory,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
    ): void {
        $customerProvider->provide('WILL.SMITH@example.com')->willThrow(CustomerNotFoundException::class);
        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('WILL.SMITH@example.com')->shouldBeCalled();

        $this->resolve('WILL.SMITH@example.com')->shouldReturn($customer);
    }

    function it_does_not_create_a_customer_if_customer_with_given_email_already_exists(
        FactoryInterface $customerFactory,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
    ): void {
        $customerProvider->provide('WILL.SMITH@example.com')->willReturn($customer);
        $customerFactory->createNew()->shouldNotBeCalled();

        $this->resolve('WILL.SMITH@example.com')->shouldReturn($customer);
    }
}
