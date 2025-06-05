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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Initializer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Initializer\CustomerInitializer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;

final class CustomerInitializerTest extends TestCase
{
    private CanonicalizerInterface&MockObject $canonicalizer;

    private CustomerInitializer $customerInitializer;

    protected function setUp(): void
    {
        $this->canonicalizer = $this->createMock(CanonicalizerInterface::class);
        $this->customerInitializer = new CustomerInitializer($this->canonicalizer);
    }

    public function testImplementsSymfonyValidatorObjectInitializerInterface(): void
    {
        $this->assertInstanceOf(ObjectInitializerInterface::class, $this->customerInitializer);
    }

    public function testSetsCanonicalEmailWhenInitializingCustomer(): void
    {
        $customer = $this->createMock(CustomerInterface::class);

        $customer->expects($this->once())->method('getEmail')->willReturn('sTeFfEn@gMaiL.CoM');

        $this->canonicalizer->expects($this->once())
            ->method('canonicalize')
            ->with('sTeFfEn@gMaiL.CoM')
            ->willReturn('steffen@gmail.com')
        ;

        $customer->expects($this->once())->method('setEmailCanonical')->with('steffen@gmail.com');

        $this->customerInitializer->initialize($customer);
    }

    public function testDoesNotSetCanonicalEmailWhenInitializingNonCustomerObject(): void
    {
        $nonCustomer = new \stdClass();

        $this->canonicalizer->expects($this->never())->method('canonicalize');

        $this->customerInitializer->initialize($nonCustomer);

        $this->assertTrue(true);
    }
}
