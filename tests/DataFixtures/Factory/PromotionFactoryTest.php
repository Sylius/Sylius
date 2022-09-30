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

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionActionFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionFactory;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PromotionRuleFactory;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Tests\PurgeDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class PromotionFactoryTest extends KernelTestCase
{
    use PurgeDatabaseTrait;
    use Factories;

    /** @test */
    function it_creates_promotion_with_default_values(): void
    {
        ChannelFactory::createMany(3);
        $promotion = PromotionFactory::createOne();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertNotNull($promotion->getCode());
        $this->assertNotNull($promotion->getName());
        $this->assertNotNull($promotion->getDescription());
        $this->assertNull($promotion->getUsageLimit());
        $this->assertFalse($promotion->isCouponBased());
        $this->assertSame(0, $promotion->getPriority());
        $this->assertNull($promotion->getStartsAt());
        $this->assertNull($promotion->getEndsAt());
        $this->assertCount(3, $promotion->getChannels());
        $this->assertCount(0, $promotion->getRules());
    }

    /** @test */
    function it_creates_promotion_with_given_code(): void
    {
        $promotion = PromotionFactory::new()->withCode('xyz')->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertSame('xyz', $promotion->getCode());
    }

    /** @test */
    function it_creates_promotion_with_given_name(): void
    {
        $promotion = PromotionFactory::new()->withName('Black Friday')->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertSame('Black Friday', $promotion->getName());
    }

    /** @test */
    function it_creates_promotion_with_given_description(): void
    {
        $promotion = PromotionFactory::new()->withDescription('This is Black Friday.')->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertSame('This is Black Friday.', $promotion->getDescription());
    }

    /** @test */
    function it_creates_promotion_with_given_usage_limit(): void
    {
        $promotion = PromotionFactory::new()->withUsageLimit(5)->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertSame(5, $promotion->getUsageLimit());
    }

    /** @test */
    function it_creates_coupon_based_promotion(): void
    {
        $promotion = PromotionFactory::new()->couponBased()->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertTrue($promotion->isCouponBased());
    }

    /** @test */
    function it_creates_not_coupon_based_promotion(): void
    {
        $promotion = PromotionFactory::new()->notCouponBased()->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertFalse($promotion->isCouponBased());
    }

    /** @test */
    function it_creates_exclusive_promotion(): void
    {
        $promotion = PromotionFactory::new()->exclusive()->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertTrue($promotion->isExclusive());
    }

    /** @test */
    function it_creates_not_exclusive_promotion(): void
    {
        $promotion = PromotionFactory::new()->notExclusive()->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertFalse($promotion->isExclusive());
    }

    /** @test */
    function it_creates_promotion_with_given_priority(): void
    {
        $promotion = PromotionFactory::new()->withPriority(42)->create();

        $this->assertInstanceOf(PromotionInterface::class, $promotion->object());
        $this->assertSame(42, $promotion->getPriority());
    }

    /** @test */
    function it_creates_catalog_promotion_with_given_start_date(): void
    {
        $startDate = new \DateTimeImmutable('today');
        $promotion = PromotionFactory::new()->withStartDate($startDate)->create();

        $this->assertEquals($startDate, $promotion->getStartsAt());
    }

    /** @test */
    function it_creates_catalog_promotion_with_given_end_date(): void
    {
        $endDate = new \DateTimeImmutable('tomorrow');
        $promotion = PromotionFactory::new()->withEndDate($endDate)->create();

        $this->assertEquals($endDate, $promotion->getEndsAt());
    }

    /** @test */
    function it_creates_shipping_method_with_given_channels(): void
    {
        $channel = ChannelFactory::new()->withCode('default')->create();
        $promotion = PromotionFactory::new()->withChannels([$channel])->create();

        $this->assertEquals('default', $promotion->getChannels()->first()->getCode());
    }

    /** @test */
    function it_creates_shipping_method_with_given_rules_as_proxy(): void
    {
        $promotionRule = PromotionRuleFactory::createOne();
        $promotion = PromotionFactory::new()->withRules([$promotionRule])->create();

        $firstRule = $promotion->getRules()->first() ?: null;
        $this->assertNotNull($firstRule);
        $this->assertEquals($promotionRule->object(), $firstRule);
    }

    /** @test */
    function it_creates_shipping_method_with_given_rules(): void
    {
        $promotionRule = PromotionRuleFactory::createOne()->object();
        $promotion = PromotionFactory::new()->withRules([$promotionRule])->create();

        $firstRule = $promotion->getRules()->first() ?: null;
        $this->assertNotNull($firstRule);
        $this->assertEquals($promotionRule, $firstRule);
    }

    /** @test */
    function it_creates_shipping_method_with_given_rules_as_array(): void
    {
        $promotion = PromotionFactory::new()->withRules([
            [
                'type' => 'item_total',
                'configuration' => [
                    'FASHION_WEB' => ['amount' => 100.00],
                ]
            ],
        ])->create();

        $firstRule = $promotion->getRules()->first() ?: null;
        $this->assertNotNull($firstRule);
        $this->assertEquals('item_total', $firstRule->getType());
        $this->assertEquals([
            'FASHION_WEB' => ['amount' => 10000],
        ], $firstRule->getConfiguration());
    }

    /** @test */
    function it_creates_shipping_method_with_given_actions_as_proxy(): void
    {
        $promotionAction = PromotionActionFactory::createOne();
        $promotion = PromotionFactory::new()->withActions([$promotionAction])->create();

        $firstAction = $promotion->getActions()->first() ?: null;
        $this->assertEquals($promotionAction->object(), $firstAction);
    }

    /** @test */
    function it_creates_shipping_method_with_given_actions(): void
    {
        $promotionAction = PromotionActionFactory::createOne()->object();
        $promotion = PromotionFactory::new()->withActions([$promotionAction])->create();

        $firstAction = $promotion->getActions()->first() ?: null;
        $this->assertEquals($promotionAction, $firstAction);
    }

    /** @test */
    function it_creates_shipping_method_with_given_actions_as_array(): void
    {
        $promotion = PromotionFactory::new()->withActions([
            [
                'configuration' => ['foo' => 'fighters'],
            ],
        ])->create();

        $firstAction = $promotion->getActions()->first();
        $this->assertEquals(['foo' => 'fighters'], $firstAction->getConfiguration());
    }

    /** @test */
    function it_creates_shipping_method_with_given_coupons(): void
    {
        $promotion = PromotionFactory::new()->withCoupons([
            [
                'code' => 'xyz',
                'per_customer_usage_limit' => 1,
                'reusable_from_cancelled_orders' => true,
                'usage_limit' => 20,
                'expires_at' => '2 days ago',
            ],
        ])->create();

        /** @var PromotionCouponInterface $firstCoupon */
        $firstCoupon = $promotion->getCoupons()->first();
        $this->assertEquals('xyz', $firstCoupon->getCode());
        $this->assertEquals(1, $firstCoupon->getPerCustomerUsageLimit());
        $this->assertTrue($firstCoupon->isReusableFromCancelledOrders());
        $this->assertEquals(20, $firstCoupon->getUsageLimit());
        $this->assertInstanceOf(\DateTimeInterface::class, $firstCoupon->getExpiresAt());
    }
}
