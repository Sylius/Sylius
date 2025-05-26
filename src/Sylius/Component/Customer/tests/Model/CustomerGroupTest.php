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

namespace Tests\Sylius\Component\Customer\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

final class CustomerGroupTest extends TestCase
{
    private CustomerGroupInterface $customerGroup;

    public function setUp(): void
    {
        parent::setUp();
        $this->customerGroup = new CustomerGroup();
    }

    public function testImplementsCustomerGroupInterface(): void
    {
        $this->assertInstanceOf(CustomerGroupInterface::class, $this->customerGroup);
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->customerGroup->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->customerGroup->setName('Retail');
        self::assertSame('Retail', $this->customerGroup->getName());
    }

    public function testCodeShouldBeMutable(): void
    {
        $this->customerGroup->setCode('#001');
        self::assertSame('#001', $this->customerGroup->getCode());
    }
}
