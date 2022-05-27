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

namespace spec\Sylius\Bundle\ApiBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerProviderSpec extends ObjectBehavior
{
    function let(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository
    ): void {
        $this->beConstructedWith($canonicalizer, $customerFactory, $customerRepository);
    }

    function it_is_a_customer_provider(): void
    {
        $this->shouldImplement(CustomerProviderInterface::class);
    }

    function it_creates_a_customer_if_there_is_no_existing_one_with_given_email(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer
    ): void {
        $canonicalizer->canonicalize('WILL.SMITH@example.com')->willReturn('will.smith@example.com');
        $customerRepository->findOneBy(['emailCanonical' => 'will.smith@example.com'])->willReturn(null);

        $customerFactory->createNew()->willReturn($customer);
        $customer->setEmail('WILL.SMITH@example.com')->shouldBeCalled();

        $this->provide('WILL.SMITH@example.com')->shouldReturn($customer);
    }

    function it_doesn_not_create_a_customer_if_customer_with_given_email_already_exists(
        CanonicalizerInterface $canonicalizer,
        FactoryInterface $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        CustomerInterface $customer
    ): void {
        $canonicalizer->canonicalize('WILL.SMITH@example.com')->willReturn('will.smith@example.com');
        $customerRepository->findOneBy(['emailCanonical' => 'will.smith@example.com'])->willReturn($customer);

        $customerFactory->createNew()->shouldNotBeCalled();

        $this->provide('WILL.SMITH@example.com')->shouldReturn($customer);
    }
}
