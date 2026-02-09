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
use Sylius\Component\Customer\Model\Customer;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class CustomerTest extends TestCase
{
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = new Customer();
    }

    public function testImplementsCustomerInterface(): void
    {
        self::assertInstanceOf(CustomerInterface::class, $this->customer);
    }

    public function testEmailShouldBeMutable(): void
    {
        $this->customer->setEmail('customer@email.com');
        self::assertSame('customer@email.com', $this->customer->getEmail());
    }

    public function testFirstNameShouldBeMutable(): void
    {
        $this->customer->setFirstName('Edward');
        self::assertSame('Edward', $this->customer->getFirstName());
    }

    public function testLastNameShouldBeMutable(): void
    {
        $this->customer->setLastName('Thatch');
        self::assertSame('Thatch', $this->customer->getLastName());
    }

    public function testFullNameShouldBeMutable(): void
    {
        $this->customer->setFirstName('Edward');
        $this->customer->setLastName('Kenway');
        self::assertSame('Edward Kenway', $this->customer->getFullName());
    }

    public function testBirthdayShouldBeMutable(): void
    {
        $birthday = new \DateTime('1987-07-08');
        $this->customer->setBirthday($birthday);
        self::assertSame($birthday, $this->customer->getBirthday());
    }

    public function testGenderShouldBeMutable(): void
    {
        $this->customer->setGender(CustomerInterface::FEMALE_GENDER);
        self::assertSame(CustomerInterface::FEMALE_GENDER, $this->customer->getGender());
    }

    public function testGenderShouldBeUnknownByDefault(): void
    {
        self::assertSame(CustomerInterface::UNKNOWN_GENDER, $this->customer->getGender());
    }

    public function testIsFemaleGender(): void
    {
        $this->customer->setGender(CustomerInterface::FEMALE_GENDER);
        self::assertTrue($this->customer->isFemale());
        self::assertFalse($this->customer->isMale());
    }

    public function testIsMaleGender(): void
    {
        $this->customer->setGender(CustomerInterface::MALE_GENDER);
        self::assertFalse($this->customer->isFemale());
        self::assertTrue($this->customer->isMale());
    }

    public function testHasNoGroupByDefault(): void
    {
        self::assertNull($this->customer->getGroup());
    }

    public function testGroupShouldBeMutable(): void
    {
        $group = $this->createMock(CustomerGroupInterface::class);
        $this->customer->setGroup($group);
        self::assertSame($group, $this->customer->getGroup());
    }
}
