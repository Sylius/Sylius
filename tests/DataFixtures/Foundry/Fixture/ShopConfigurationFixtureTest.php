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

namespace Sylius\Tests\DataFixtures\Foundry\Fixture;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class ShopConfigurationFixtureTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_shop_configuration(): void
    {
        self::bootKernel();

        /** @var Fixture $fixture */
        $fixture = static::getContainer()->get('sylius.shop_fixtures.foundry.fixture.shop_configuration');

        $fixture->load(static::getContainer()->get('doctrine.orm.entity_manager'));

        $locales = $this->getLocaleRepository()->findAll();
        $currencies = $this->getCurrencyRepository()->findAll();
        $countries = $this->getCountryRepository()->findAll();
        $customerGroups = $this->getCustomerGroupRepository()->findAll();
        $customers = $this->getCustomerRepository()->findAll();

        $this->assertCount(8, $locales);
        $this->assertCount(9, $currencies);
        $this->assertCount(12, $countries);
        $this->assertCount(2, $customerGroups);
        $this->assertCount(1, $customers);
    }

    private function getCurrencyRepository(): RepositoryInterface
    {
        return static::getContainer()->get('sylius.repository.currency');
    }

    private function getCustomerRepository(): RepositoryInterface
    {
        return static::getContainer()->get('sylius.repository.customer');
    }

    private function getCustomerGroupRepository(): RepositoryInterface
    {
        return static::getContainer()->get('sylius.repository.customer_group');
    }

    private function getCountryRepository(): RepositoryInterface
    {
        return static::getContainer()->get('sylius.repository.country');
    }

    private function getLocaleRepository(): RepositoryInterface
    {
        return static::getContainer()->get('sylius.repository.locale');
    }
}
