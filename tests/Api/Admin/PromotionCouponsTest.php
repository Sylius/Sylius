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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PromotionCouponsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->client->request(
            method: 'GET',
            uri: sprintf(sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode())),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/get_promotion_coupon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_specific_promotion_coupons(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        $this->client->request(method: 'GET', uri: sprintf('/api/v2/admin/promotions/%s/coupons', $promotion->getCode()), server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/get_promotion_coupons_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons', $promotion->getCode()),
            server: $header,
            content: json_encode([
                'code' => 'XYZ3',
                'usageLimit' => 100,
                'perCustomerUsageLimit' => 3,
                'reusableFromCancelledOrders' => false,
                'expiresAt' => '23-12-2023',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/post_promotion_coupon_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()),
            server: $header,
            content: json_encode([
                'usageLimit' => 1000,
                'perCustomerUsageLimit' => 5,
                'reusableFromCancelledOrders' => false,
                'expiresAt' => '2020-01-01 12:00:00',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/put_promotion_coupon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_generates_a_promotion_coupons(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'POST',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons/generate', $promotion->getCode()),
            server: $header,
            content: json_encode([
                'amount' => 4,
                'prefix' => 'ABC',
                'codeLength' => 6,
                'suffix' => 'XYZ',
                'usageLimit' => 10,
                'expiresAt' => '2020-01-01 12:00:00',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/generate_promotion_coupons_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_generate_promotion_coupons_with_non_existing_promotion_code(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/promotions/non_existing_promotion_code/coupons/generate',
            server: $header,
            content: json_encode([
                'amount' => 4,
                'prefix' => 'ABC',
                'codeLength' => 6,
                'suffix' => 'XYZ',
                'usageLimit' => 10,
                'expiresAt' => '2020-01-01 12:00:00',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/generate_promotion_coupons_with_invalid_promotion_code_response',
            Response::HTTP_NOT_FOUND,
        );
    }

    /** @test */
    public function it_removes_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_delete_the_promotion_coupon_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'promotion/promotion.yaml',
            'promotion/promotion_order.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
