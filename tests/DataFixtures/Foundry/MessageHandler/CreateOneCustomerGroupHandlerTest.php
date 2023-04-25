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

namespace Sylius\Tests\DataFixtures\Foundry\MessageHandler;

use Sylius\Bundle\CoreBundle\ShopFixtures\Command\CreateOneCustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroup;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class CreateOneCustomerGroupHandlerTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_customer_group_with_random_code_and_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerGroup|Proxy $customerGroup */
        $customerGroup = $bus->dispatch(new CreateOneCustomerGroup());

        $this->assertInstanceOf(CustomerGroupInterface::class, $customerGroup->object());
        $this->assertNotNull($customerGroup->getCode());
        $this->assertNotNull($customerGroup->getName());
    }

    /** @test */
    function it_creates_customer_group_with_given_code(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerGroup|Proxy $customerGroup */
        $customerGroup = $bus->dispatch((new CreateOneCustomerGroup())->withCode('group_a'));

        $this->assertInstanceOf(CustomerGroupInterface::class, $customerGroup->object());
        $this->assertEquals('group_a', $customerGroup->getCode());
    }

    /** @test */
    function it_creates_customer_group_with_given_name(): void
    {
        self::bootKernel();

        /** @var MessageBusInterface $bus */
        $bus = static::getContainer()->get('sylius.shop_fixtures.bus.command');

        /** @var CustomerGroup|Proxy $customerGroup */
        $customerGroup = $bus->dispatch((new CreateOneCustomerGroup())->withName('Group A'));

        $this->assertInstanceOf(CustomerGroupInterface::class, $customerGroup->object());
        $this->assertEquals('Group A', $customerGroup->getName());
    }
}
