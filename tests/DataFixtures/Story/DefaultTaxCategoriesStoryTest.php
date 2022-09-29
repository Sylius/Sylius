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
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultTaxCategoriesStoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Story\DefaultTaxRatesStoryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class DefaultTaxCategoriesStoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    public function it_creates_default_tax_categories(): void
    {
        /** @var DefaultTaxCategoriesStoryInterface $defaultTaxCategoriesStory */
        $defaultTaxCategoriesStory = self::getContainer()->get('sylius.data_fixtures.story.default_tax_categories');

        $defaultTaxCategoriesStory->build();

        $taxCategory = $this->getTaxCategoryByCode('clothing');
        $this->assertNotNull($taxCategory, sprintf('Tax category "%s" was not found but it should.', 'clothing'));
        $this->assertEquals('Clothing', $taxCategory->getName());

        $taxCategory = $this->getTaxCategoryByCode('other');
        $this->assertNotNull($taxCategory, sprintf('Tax category "%s" was not found but it should.', 'other'));
        $this->assertEquals('Other', $taxCategory->getName());
    }

    private function getTaxCategoryByCode(string $code): ?TaxCategoryInterface
    {
        /** @var RepositoryInterface $taxCategoryRepository */
        $taxCategoryRepository = self::getContainer()->get('sylius.repository.tax_category');

        return $taxCategoryRepository->findOneBy(['code' => $code]);
    }
}
