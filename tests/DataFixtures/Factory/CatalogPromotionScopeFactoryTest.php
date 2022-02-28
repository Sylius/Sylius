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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionScopeFactory;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CatalogPromotionScopeFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_catalog_promotion_scope(): void
    {
        $catalogPromotionScope = CatalogPromotionScopeFactory::createOne();

        $this->assertInstanceOf(CatalogPromotionScopeInterface::class, $catalogPromotionScope->object());
        $this->assertEquals('for_products', $catalogPromotionScope->getType());
        $this->assertEquals([], $catalogPromotionScope->getConfiguration());
    }

    /** @test */
    function it_creates_catalog_promotion_scope_with_given_type(): void
    {
        $catalogPromotionScope = CatalogPromotionScopeFactory::new()->withType(InForTaxonsScopeVariantChecker::TYPE)->create();

        $this->assertEquals(InForTaxonsScopeVariantChecker::TYPE, $catalogPromotionScope->getType());
    }

    /** @test */
    function it_creates_catalog_promotion_scope_with_given_configuration(): void
    {
        $country = CatalogPromotionScopeFactory::new()->withConfiguration(['foo' => 'fighters'])->create();

        $this->assertEquals(['foo' => 'fighters'], $country->getConfiguration());
    }
}
