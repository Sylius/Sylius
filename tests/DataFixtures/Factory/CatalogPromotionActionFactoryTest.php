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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\CatalogPromotionActionFactory;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class CatalogPromotionActionFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_catalog_promotion_action(): void
    {
        $catalogPromotionAction = CatalogPromotionActionFactory::createOne();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction->object());
        $this->assertEquals('percentage_discount', $catalogPromotionAction->getType());
        $this->assertEquals([], $catalogPromotionAction->getConfiguration());
    }

    /** @test */
    function it_creates_catalog_promotion_action_with_given_type(): void
    {
        $catalogPromotionAction = CatalogPromotionActionFactory::new()->withType('fixed_discount')->create();

        $this->assertEquals('fixed_discount', $catalogPromotionAction->getType());
    }

    /** @test */
    function it_creates_catalog_promotion_action_with_given_configuration(): void
    {
        $catalogPromotionAction = CatalogPromotionActionFactory::new()->withConfiguration(['foo' => 'fighters'])->create();

        $this->assertEquals(['foo' => 'fighters'], $catalogPromotionAction->getConfiguration());
    }

    /** @test */
    function it_transforms_configuration_amount_on_fixed_discount_price(): void
    {
        $catalogPromotionAction = CatalogPromotionActionFactory::new()
            ->withType(FixedDiscountPriceCalculator::TYPE)
            ->withConfiguration(['default_channel' => ['amount' => 1]])
            ->create();

        $this->assertEquals(['default_channel' => ['amount' => 100]], $catalogPromotionAction->getConfiguration());
    }
}
