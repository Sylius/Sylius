<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Promotion\Exception\PromotionUsageLimitReachedException;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class OrderPromotionsUsageModifierConcurrencyTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    private OrderPromotionsUsageModifierInterface $usageModifier;

    private Connection $concurrentConnection;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->usageModifier = self::getContainer()->get('sylius.modifier.promotion.order_usage');
        $this->concurrentConnection = DriverManager::getConnection($this->entityManager->getConnection()->getParams());
    }

    protected function tearDown(): void
    {
        $this->concurrentConnection->close();

        parent::tearDown();
    }

    #[Test]
    public function it_increments_promotion_and_coupon_usage(): void
    {
        $cart = $this->prepareCart(couponUsageLimit: 1, promotionUsageLimit: 1);

        $this->incrementUsage($cart);

        $this->assertSame(1, $this->getUsed('sylius_promotion_coupon'));
        $this->assertSame(1, $this->getUsed('sylius_promotion'));
    }

    #[Test]
    public function it_does_not_exceed_coupon_usage_limit_when_concurrent_order_used_the_coupon_after_eligibility_check(): void
    {
        $cart = $this->prepareCart(couponUsageLimit: 1);

        $this->concurrentConnection->executeStatement('UPDATE sylius_promotion_coupon SET used = used + 1');

        try {
            $this->incrementUsage($cart);
            $this->fail('The coupon usage limit has been exceeded.');
        } catch (PromotionUsageLimitReachedException) {
        }

        $this->assertSame(1, $this->getUsed('sylius_promotion_coupon'));
    }

    #[Test]
    public function it_does_not_exceed_promotion_usage_limit_when_concurrent_order_used_the_promotion_after_eligibility_check(): void
    {
        $cart = $this->prepareCart(promotionUsageLimit: 1);

        $this->concurrentConnection->executeStatement('UPDATE sylius_promotion SET used = used + 1');

        try {
            $this->incrementUsage($cart);
            $this->fail('The promotion usage limit has been exceeded.');
        } catch (PromotionUsageLimitReachedException) {
        }

        $this->assertSame(1, $this->getUsed('sylius_promotion'));
    }

    #[Test]
    public function it_does_not_exceed_coupon_per_customer_usage_limit_when_concurrent_order_of_the_same_customer_was_placed_after_eligibility_check(): void
    {
        $cart = $this->prepareCart(couponPerCustomerUsageLimit: 1);

        $this->concurrentConnection->executeStatement(
            'UPDATE sylius_order SET state = :state, checkout_completed_at = :completedAt WHERE id <> :cartId',
            [
                'cartId' => $cart->getId(),
                'completedAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'state' => OrderInterface::STATE_NEW,
            ],
        );

        $this->expectException(PromotionUsageLimitReachedException::class);

        $this->incrementUsage($cart);
    }

    private function prepareCart(
        ?int $couponUsageLimit = null,
        ?int $couponPerCustomerUsageLimit = null,
        ?int $promotionUsageLimit = null,
    ): OrderInterface {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');
        $fixtures = $fixtureLoader->load(
            [__DIR__ . '/../DataFixtures/ORM/resources/promotion_usage_limit.yml'],
            [],
            [],
            PurgeMode::createDeleteMode(),
        );

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['coupon'];
        $coupon->setUsageLimit($couponUsageLimit);
        $coupon->setPerCustomerUsageLimit($couponPerCustomerUsageLimit);
        $coupon->getPromotion()->setUsageLimit($promotionUsageLimit);
        $this->entityManager->flush();
        $this->entityManager->clear();

        /** @var OrderInterface $cart */
        $cart = $this->entityManager->find($fixtures['cart']::class, $fixtures['cart']->getId());

        $this->assertSame(0, $cart->getPromotionCoupon()->getUsed());
        $this->assertSame(0, $cart->getPromotions()->first()->getUsed());

        return $cart;
    }

    private function incrementUsage(OrderInterface $cart): void
    {
        $this->entityManager->wrapInTransaction(function () use ($cart): void {
            $this->usageModifier->increment($cart);
        });
    }

    private function getUsed(string $table): int
    {
        return (int) $this->concurrentConnection->fetchOne(sprintf('SELECT used FROM %s', $table));
    }
}
