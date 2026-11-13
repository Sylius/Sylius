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

namespace Sylius\Tests\Api\Shop\Checkout;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

final class PromotionUsageLimitConcurrencyTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    private const COUPON_CODE = 'XYZ2';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    #[Test]
    public function it_rejects_order_when_coupon_usage_limit_is_reached_sequentially(): void
    {
        $this->loadFixtures(couponUsageLimit: 1);

        $this->prepareCartWithCoupon('token-a', 'a@example.com');
        $this->prepareCartWithCoupon('token-b', 'b@example.com');

        $this->completeOrder('token-a');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->completeOrder('token-b');

        $this->assertNotSame('completed', $this->getCheckoutState('token-b'));
        $this->assertSame(1, $this->getCouponUsed());
    }

    #[Test]
    public function it_rejects_order_when_coupon_usage_limit_is_reached_by_concurrent_order(): void
    {
        $this->loadFixtures(couponUsageLimit: 1);

        $this->prepareCartWithCoupon('token-b', 'b@example.com');

        $this->simulateConcurrentCouponUsageBeforeUsageIsIncremented();

        $this->completeOrder('token-b');

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
        $this->assertNotSame('completed', $this->getCheckoutState('token-b'));
        $this->assertLessThanOrEqual(1, $this->getCouponUsed());
    }

    private function loadFixtures(int $couponUsageLimit): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'promotion/promotion.yaml',
        ]);

        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->get('sylius.repository.promotion_coupon')->findOneBy(['code' => self::COUPON_CODE]);
        $coupon->setUsageLimit($couponUsageLimit);
        $coupon->setUsed(0);

        $this->getEntityManager()->flush();
    }

    private function prepareCartWithCoupon(string $tokenValue, string $email): void
    {
        $this->pickUpCart(tokenValue: $tokenValue, email: $email);
        $this->addItemToCart('MUG_BLUE', 1, $tokenValue, $email);
        $cart = $this->updateCartWithAddressAndCouponCode($tokenValue, $email, self::COUPON_CODE);
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'UPS', $cart->getShipments()->first()->getId());
        $cart = $this->dispatchPaymentMethodChooseCommand($tokenValue, 'CASH_ON_DELIVERY', $cart->getLastPayment()->getId());

        $this->assertSame(self::COUPON_CODE, $cart->getPromotionCoupon()?->getCode());
        $this->assertLessThan(0, $cart->getOrderPromotionTotal());
    }

    private function simulateConcurrentCouponUsageBeforeUsageIsIncremented(): void
    {
        $connection = $this->getConnection();

        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer()->get('event_dispatcher');
        $dispatcher->addListener(
            'workflow.sylius_order_checkout.transition.complete',
            static function () use ($connection): void {
                $connection->executeStatement(
                    'UPDATE sylius_promotion_coupon SET used = used + 1 WHERE code = :code',
                    ['code' => self::COUPON_CODE],
                );
            },
        );
    }

    private function completeOrder(string $tokenValue): void
    {
        $this->getEntityManager()->clear();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: '{}',
        );
    }

    private function getCheckoutState(string $tokenValue): string
    {
        return (string) $this->getConnection()->fetchOne(
            'SELECT checkout_state FROM sylius_order WHERE token_value = :token',
            ['token' => $tokenValue],
        );
    }

    private function getCouponUsed(): int
    {
        return (int) $this->getConnection()->fetchOne(
            'SELECT used FROM sylius_promotion_coupon WHERE code = :code',
            ['code' => self::COUPON_CODE],
        );
    }

    private function getConnection(): Connection
    {
        return self::getContainer()->get('doctrine.dbal.default_connection');
    }
}
