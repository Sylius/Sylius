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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionRuleFactory;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class PromotionRuleFactoryTest extends KernelTestCase
{
    use ResetDatabase;
    use Factories;

    /** @test */
    function it_creates_promotion_rule(): void
    {
        $promotionRule = PromotionRuleFactory::createOne();

        $this->assertInstanceOf(PromotionRuleInterface::class, $promotionRule->object());
        $this->assertEquals('cart_quantity', $promotionRule->getType());
        $this->assertArrayHasKey('count', $promotionRule->getConfiguration());
    }

    /** @test */
    function it_creates_promotion_rule_with_given_type(): void
    {
        $promotionRule = PromotionRuleFactory::new()->withType(ItemTotalRuleChecker::TYPE)->create();

        $this->assertEquals(ItemTotalRuleChecker::TYPE, $promotionRule->getType());
    }

    /** @test */
    function it_creates_promotion_rule_with_given_configuration(): void
    {
        $promotionRule = PromotionRuleFactory::new()->withConfiguration(['foo' => 'fighters'])->create();

        $this->assertEquals(['foo' => 'fighters'], $promotionRule->getConfiguration());
    }

    /** @test */
    function it_transforms_the_amount(): void
    {
        $promotionRule = PromotionRuleFactory::new()->withConfiguration(['default_channel' => ['amount' => 1]])->create();

        $this->assertEquals(['default_channel' => ['amount' => 100]], $promotionRule->getConfiguration());
    }
}
