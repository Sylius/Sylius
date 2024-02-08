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

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Provider\CustomerProvider;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerProviderSpec extends ObjectBehavior
{
    function let(CustomerRepositoryInterface $customerRepository, CanonicalizerInterface $canonicalizer): void
    {
        $this->beConstructedWith($customerRepository, $canonicalizer);
    }

    function it_implements_customer_interface(): void
    {
        $this->shouldImplement(CustomerProvider::class);
    }

    function it_provides_customer(
        CustomerRepositoryInterface $customerRepository,
        CanonicalizerInterface $canonicalizer,
        CustomerInterface $customer,
    ): void {
        $canonicalizer->canonicalize('Adam@syLius.com')->willReturn('adam@sylius.com');
        $customerRepository->findOneBy(['emailCanonical' => 'adam@sylius.com'])->willReturn($customer);

        $this->provide('Adam@syLius.com')->shouldReturn($customer);
    }

    function it_throws_exception_if_customer_is_not_found(
        CanonicalizerInterface $canonicalizer,
        CustomerRepositoryInterface $customerRepository,
    ): void {
        $canonicalizer->canonicalize('Adam@syLius.com')->willReturn('adam@sylius.com');
        $customerRepository->findOneBy(['emailCanonical' => 'adam@sylius.com'])->willReturn(null);

        $this->shouldThrow(CustomerNotFoundException::class)->during('provide', ['Adam@syLius.com']);
    }
}
