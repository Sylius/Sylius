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

namespace Sylius\Tests\DataFixtures\Factory;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CustomerGroupFactory;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CustomerGroupFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_customer_group_with_random_code_and_name(): void
    {
        $customerGroup = CustomerGroupFactory::createOne();

        $this->assertInstanceOf(CustomerGroupInterface::class, $customerGroup->object());
        $this->assertNotNull($customerGroup->getCode());
        $this->assertNotNull($customerGroup->getName());
    }

    /** @test */
    function it_creates_customer_group_with_given_code(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->create();

        $this->assertEquals('group_a', $customerGroup->getCode());
    }

    /** @test */
    function it_creates_customer_group_with_given_name(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withName('Group A')->create();

        $this->assertEquals('Group A', $customerGroup->getName());
    }
}
