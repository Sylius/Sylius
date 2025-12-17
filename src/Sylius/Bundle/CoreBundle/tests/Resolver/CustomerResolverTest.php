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

namespace Tests\Sylius\Bundle\CoreBundle\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolver;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class CustomerResolverTest extends TestCase
{
    private FactoryInterface&MockObject $customerFactory;

    private CustomerProviderInterface&MockObject $customerProvider;

    private CustomerResolver $customerResolver;

    protected function setUp(): void
    {
        $this->customerFactory = $this->createMock(FactoryInterface::class);
        $this->customerProvider = $this->createMock(CustomerProviderInterface::class);

        $this->customerResolver = new CustomerResolver(
            $this->customerFactory,
            $this->customerProvider,
        );
    }

    public function testImplementsCustomerResolverInterface(): void
    {
        $this->assertInstanceOf(CustomerResolverInterface::class, $this->customerResolver);
    }

    public function testCreatesCustomerIfNoneExistsForEmail(): void
    {
        $email = 'WILL.SMITH@example.com';
        $customer = $this->createMock(CustomerInterface::class);

        $this->customerProvider
            ->method('provide')
            ->with($email)
            ->willThrowException(new CustomerNotFoundException())
        ;

        $this->customerFactory->expects($this->once())->method('createNew')->willReturn($customer);
        $customer->expects($this->once())->method('setEmail')->with($email);

        $resolvedCustomer = $this->customerResolver->resolve($email);

        $this->assertSame($customer, $resolvedCustomer);
    }

    public function testReturnsExistingCustomerIfFound(): void
    {
        $email = 'WILL.SMITH@example.com';
        $customer = $this->createMock(CustomerInterface::class);

        $this->customerProvider->expects($this->once())->method('provide')->with($email)->willReturn($customer);
        $this->customerFactory->expects($this->never())->method('createNew');

        $resolvedCustomer = $this->customerResolver->resolve($email);

        $this->assertSame($customer, $resolvedCustomer);
    }
}
