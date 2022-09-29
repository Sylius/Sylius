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

namespace Sylius\Tests\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultAdminUsersStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultTaxRatesStoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultTaxRatesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_tax_rates(): void
    {
        /** @var DefaultTaxRatesStoryInterface $defaultTaxRatesStory */
        $defaultTaxRatesStory = self::getContainer()->get('sylius.data_fixtures.story.default_tax_rates');

        $defaultTaxRatesStory->build();

        $taxRate = $this->getTaxRateByCode('clothing_sales_tax_7');
        $this->assertNotNull($taxRate, sprintf('Tax rate "%s" was not found but it should.', 'clothing_sales_tax_7'));
        $this->assertEquals('Clothing Sales Tax 7%', $taxRate->getName());
        $this->assertEquals('US', $taxRate->getZone()?->getCode());
        $this->assertEquals('clothing', $taxRate->getCategory()?->getCode());
        $this->assertEquals('0.07', $taxRate->getAmount());

        $taxRate = $this->getTaxRateByCode('sales_tax_20');
        $this->assertNotNull($taxRate, sprintf('Tax rate "%s" was not found but it should.', 'sales_tax_20'));
        $this->assertEquals('Sales Tax 20%', $taxRate->getName());
        $this->assertEquals('US', $taxRate->getZone()?->getCode());
        $this->assertEquals('other', $taxRate->getCategory()?->getCode());
        $this->assertEquals('0.2', $taxRate->getAmount());
    }

    private function getTaxRateByCode(string $code): ?TaxRateInterface
    {
        /** @var RepositoryInterface $taxRateRepository */
        $taxRateRepository = self::getContainer()->get('sylius.repository.tax_rate');

        return $taxRateRepository->findOneBy(['code' => $code]);
    }
}
