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

use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Symfony\Component\HttpFoundation\Response;

final class PromotionCouponUsageLimitTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();
    }

    #[Test]
    public function it_does_not_complete_order_when_coupon_has_reached_its_usage_limit_after_being_applied(): void
    {
        $this->loadFixtures(usageLimit: 1, perCustomerUsageLimit: null);
        $this->prepareCartWithCoupon('token', 'oliver@doe.com');
        $this->placeOrder('another-token', 'john@doe.com', couponCode: 'XYZ2');

        $this->completeOrder('token');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertOrderIsNotCompletedWithoutCoupon('token');
    }

    #[Test]
    public function it_completes_order_without_coupon_that_has_reached_its_usage_limit_after_being_applied(): void
    {
        $this->loadFixtures(usageLimit: 1, perCustomerUsageLimit: null);
        $this->prepareCartWithCoupon('token', 'oliver@doe.com');
        $this->placeOrder('another-token', 'john@doe.com', couponCode: 'XYZ2');
        $this->completeOrder('token');

        $this->completeOrder('token');

        $this->assertResponseIsSuccessful();
        $this->assertOrderIsCompletedWithoutCoupon('token');
        $this->assertCouponUsed(1);
    }

    #[Test]
    public function it_does_not_complete_order_when_coupon_has_reached_its_per_customer_usage_limit_after_being_applied(): void
    {
        $this->loadFixtures(usageLimit: null, perCustomerUsageLimit: 1);
        $this->prepareCartWithCoupon('token', 'oliver@doe.com');
        $this->placeOrder('another-token', 'oliver@doe.com', couponCode: 'XYZ2');

        $this->completeOrder('token');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertOrderIsNotCompletedWithoutCoupon('token');
    }

    #[Test]
    public function it_completes_order_without_coupon_that_has_reached_its_per_customer_usage_limit_after_being_applied(): void
    {
        $this->loadFixtures(usageLimit: null, perCustomerUsageLimit: 1);
        $this->prepareCartWithCoupon('token', 'oliver@doe.com');
        $this->placeOrder('another-token', 'oliver@doe.com', couponCode: 'XYZ2');
        $this->completeOrder('token');

        $this->completeOrder('token');

        $this->assertResponseIsSuccessful();
        $this->assertOrderIsCompletedWithoutCoupon('token');
        $this->assertCouponUsed(1);
    }

    private function loadFixtures(?int $usageLimit, ?int $perCustomerUsageLimit): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
            'promotion/promotion.yaml',
        ]);

        $coupon = $this->findCoupon();
        $coupon->setUsageLimit($usageLimit);
        $coupon->setPerCustomerUsageLimit($perCustomerUsageLimit);
        $coupon->setUsed(0);

        $this->get('doctrine.orm.entity_manager')->flush();
    }

    private function prepareCartWithCoupon(string $tokenValue, string $email): void
    {
        $this->pickUpCart(tokenValue: $tokenValue, email: $email);
        $this->addItemToCart('MUG_BLUE', 1, $tokenValue, $email);
        $cart = $this->updateCartWithAddressAndCouponCode($tokenValue, $email, 'XYZ2');
        $this->dispatchShippingMethodChooseCommand($tokenValue, 'UPS', $cart->getShipments()->first()->getId());
        $cart = $this->dispatchPaymentMethodChooseCommand($tokenValue, 'CASH_ON_DELIVERY', $cart->getLastPayment()->getId());

        $this->assertSame('XYZ2', $cart->getPromotionCoupon()?->getCode());
        $this->assertLessThan(0, $cart->getOrderPromotionTotal());
    }

    private function completeOrder(string $tokenValue): void
    {
        $this->get('doctrine.orm.entity_manager')->clear();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/complete', $tokenValue),
            server: $this->headerBuilder()->withMergePatchJsonContentType()->withJsonLdAccept()->build(),
            content: '{}',
        );
    }

    private function assertOrderIsNotCompletedWithoutCoupon(string $tokenValue): void
    {
        $order = $this->findOrder($tokenValue);

        $this->assertSame(OrderInterface::STATE_CART, $order->getState());
        $this->assertNull($order->getPromotionCoupon());
        $this->assertSame(0, $order->getOrderPromotionTotal());
    }

    private function assertOrderIsCompletedWithoutCoupon(string $tokenValue): void
    {
        $order = $this->findOrder($tokenValue);

        $this->assertSame(OrderInterface::STATE_NEW, $order->getState());
        $this->assertNull($order->getPromotionCoupon());
        $this->assertSame(0, $order->getOrderPromotionTotal());
    }

    private function assertCouponUsed(int $expectedUsed): void
    {
        $this->assertSame($expectedUsed, $this->findCoupon()->getUsed());
    }

    private function findOrder(string $tokenValue): OrderInterface
    {
        $this->get('doctrine.orm.entity_manager')->clear();

        /** @var OrderInterface $order */
        $order = $this->get('sylius.repository.order')->findOneBy(['tokenValue' => $tokenValue]);

        return $order;
    }

    private function findCoupon(): PromotionCouponInterface
    {
        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->get('sylius.repository.promotion_coupon')->findOneBy(['code' => 'XYZ2']);

        return $coupon;
    }
}
