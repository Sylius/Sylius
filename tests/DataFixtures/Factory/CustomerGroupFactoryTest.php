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

final class CustomerGroupFactoryTest extends KernelTestCase
{
    use Factories;

    /** @test */
    function it_creates_customer_groups(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withoutPersisting()->create();

        $this->assertInstanceOf(CustomerGroupInterface::class, $customerGroup->object());
    }

    /** @test */
    function it_creates_customer_groups_with_codes(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withCode('group_a')->withoutPersisting()->create();

        $this->assertEquals('group_a', $customerGroup->getCode());

        $customerGroup = CustomerGroupFactory::new()->withCode()->withoutPersisting()->create();

        $this->assertNotNull($customerGroup->getCode());

        $customerGroup = CustomerGroupFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($customerGroup->getCode());
    }

    /** @test */
    function it_creates_customer_groups_with_names(): void
    {
        $customerGroup = CustomerGroupFactory::new()->withName('Group A')->withoutPersisting()->create();

        $this->assertEquals('Group A', $customerGroup->getName());

        $customerGroup = CustomerGroupFactory::new()->withName()->withoutPersisting()->create();

        $this->assertNotNull($customerGroup->getName());

        $customerGroup = CustomerGroupFactory::new()->withoutPersisting()->create();

        $this->assertNotNull($customerGroup->getName());
    }
}
