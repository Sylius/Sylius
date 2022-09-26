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
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionActionFactory;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PromotionActionFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_promotion_action_with_default_values(): void
    {
        $promotionAction = PromotionActionFactory::createOne();

        $this->assertInstanceOf(PromotionActionInterface::class, $promotionAction->object());
        $this->assertEquals('order_percentage_discount', $promotionAction->getType());
        $this->assertCount(1, $promotionAction->getConfiguration());
    }

    /** @test */
    function it_creates_promotion_action_with_given_type(): void
    {
        $promotionAction = PromotionActionFactory::new()->withType('order_fixed_discount')->create();

        $this->assertEquals('order_fixed_discount', $promotionAction->getType());
    }

    /** @test */
    function it_creates_promotion_action_with_given_configuration(): void
    {
        $promotionAction = PromotionActionFactory::new()->withConfiguration(['foo' => 'fighters'])->create();

        $this->assertEquals(['foo' => 'fighters'], $promotionAction->getConfiguration());
    }

    /** @test */
    function it_transforms_configuration_amount(): void
    {
        $promotionAction = PromotionActionFactory::new()
            ->withConfiguration(['default_channel' => ['amount' => 1]])
            ->create();

        $this->assertEquals(['default_channel' => ['amount' => 100]], $promotionAction->getConfiguration());
    }

    /** @test */
    function it_transforms_configuration_percentage(): void
    {
        $promotionAction = PromotionActionFactory::new()
            ->withConfiguration(['default_channel' => ['percentage' => 10]])
            ->create();

        $this->assertEquals(['default_channel' => ['percentage' => 0.1]], $promotionAction->getConfiguration());
    }
}
