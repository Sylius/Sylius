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
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $coupon = $fixtures['promotion_1_off_coupon_1'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/promotion-coupons/%s', $coupon->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/get_promotion_coupon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_promotion_coupons(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/promotion-coupons', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion_coupon/get_promotion_coupons_response',
            Response::HTTP_OK,
        );
    }
}
