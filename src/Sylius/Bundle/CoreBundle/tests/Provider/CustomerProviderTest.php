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

namespace Tests\Sylius\Bundle\CoreBundle\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Provider\CustomerProvider;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;

final class CustomerProviderTest extends TestCase
{
    private CustomerRepositoryInterface&MockObject $customerRepository;

    private CanonicalizerInterface&MockObject $canonicalizerMock;

    private CustomerProvider $customerProvider;

    protected function setUp(): void
    {
        $this->customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $this->canonicalizerMock = $this->createMock(CanonicalizerInterface::class);
        $this->customerProvider = new CustomerProvider($this->customerRepository, $this->canonicalizerMock);
    }

    public function testImplementsCustomerInterface(): void
    {
        $this->assertInstanceOf(CustomerProvider::class, $this->customerProvider);
    }

    public function testProvidesCustomer(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $this->canonicalizerMock
            ->expects($this->once())
            ->method('canonicalize')
            ->with('Adam@syLius.com')
            ->willReturn('adam@sylius.com')
        ;

        $this->customerRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['emailCanonical' => 'adam@sylius.com'])
            ->willReturn($customer)
        ;

        $this->assertSame($customer, $this->customerProvider->provide('Adam@syLius.com'));
    }

    public function testThrowsExceptionIfCustomerIsNotFound(): void
    {
        $this->canonicalizerMock
            ->expects($this->once())
            ->method('canonicalize')
            ->with('Adam@syLius.com')
            ->willReturn('adam@sylius.com')
        ;

        $this->customerRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['emailCanonical' => 'adam@sylius.com'])
            ->willReturn(null)
        ;

        $this->expectException(CustomerNotFoundException::class);

        $this->customerProvider->provide('Adam@syLius.com');
    }
}
