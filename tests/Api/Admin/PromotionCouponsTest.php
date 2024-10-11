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
use Symfony\Component\HttpFoundation\Response;

final class PromotionCouponsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();

        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPutHeaders();
        $this->setUpDefaultDeleteHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->requestGet(sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()));

        $this->assertResponseSuccessful('admin/promotion_coupon/get_promotion_coupon_response');
    }

    /** @test */
    public function it_gets_promotion_coupons_for_specific_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        $this->requestGet(sprintf('/api/v2/admin/promotions/%s/coupons', $promotion->getCode()));

        $this->assertResponseSuccessful('admin/promotion_coupon/get_promotion_coupons_response');
    }

    /** @test */
    public function it_gets_an_empty_array_result_when_the_promotion_has_not_exist(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->requestGet('/api/v2/admin/promotions/NON_EXISTING_PROMOTION_CODE/coupons');

        $this->assertResponseSuccessful('admin/promotion_coupon/get_empty_promotion_coupons_response');
    }

    /** @test */
    public function it_creates_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        $this->requestPost(
            sprintf('/api/v2/admin/promotions/%s/coupons', $promotion->getCode()),
            [
                'code' => 'XYZ3',
                'usageLimit' => 100,
                'perCustomerUsageLimit' => 3,
                'reusableFromCancelledOrders' => false,
                'expiresAt' => '23-12-2023',
            ],
        );

        $this->assertResponseCreated('admin/promotion_coupon/post_promotion_coupon_response');
    }

    /** @test */
    public function it_does_not_create_promotion_coupon_if_promotion_does_not_exist(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->requestPost(
            '/api/v2/admin/promotions/NON_EXISTING_PROMOTION_CODE/coupons',
            [
                'code' => 'XYZ3',
                'usageLimit' => 100,
                'perCustomerUsageLimit' => 3,
                'reusableFromCancelledOrders' => false,
                'expiresAt' => '23-12-2023',
            ],
        );

        $this->assertResponseNotFound('Parent resource not found.');
    }

    /** @test */
    public function it_updates_a_promotion_coupon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->requestPut(
            sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()),
            [
                'usageLimit' => 1000,
                'perCustomerUsageLimit' => 5,
                'reusableFromCancelledOrders' => false,
                'expiresAt' => '2020-01-01 12:00:00',
            ],
        );

        $this->assertResponseSuccessful('admin/promotion_coupon/put_promotion_coupon_response');
    }

    /** @test */
    public function it_generates_promotion_coupons(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->requestPost(
            sprintf('/api/v2/admin/promotions/%s/coupons/generate', $promotion->getCode()),
            [
                'amount' => 4,
                'prefix' => 'ABC',
                'codeLength' => 6,
                'suffix' => 'XYZ',
                'usageLimit' => 10,
                'expiresAt' => '2020-01-01 12:00:00',
            ],
        );

        $this->assertResponseCreated('admin/promotion_coupon/generate_promotion_coupons_response');
    }

    /** @test */
    public function it_does_not_generate_promotion_coupons_with_non_existing_promotion_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml']);

        $this->requestPost(
            '/api/v2/admin/promotions/non_existing_promotion_code/coupons/generate',
            [
                'amount' => 4,
                'prefix' => 'ABC',
                'codeLength' => 6,
                'suffix' => 'XYZ',
                'usageLimit' => 10,
                'expiresAt' => '2020-01-01 12:00:00',
            ],
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

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->requestDelete(sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_delete_a_promotion_coupon_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'promotion/promotion.yaml', 'promotion/promotion_order.yaml']);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        /** @var PromotionCouponInterface $coupon */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->requestDelete(sprintf('/api/v2/admin/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
