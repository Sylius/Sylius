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
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PromotionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/get_promotion_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/promotions', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/get_promotions_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_promotion(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/promotions',
            server: $header,
            content: json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'description' => 'T-Shirts discount',
                'priority' => 22,
                'appliesToDiscounted' => false,
                'exclusive' => true,
                "usageLimit" => 3,
                "couponBased" => true,
                'startsAt' => '2023-10-04 12:30:00',
                'endsAt' => '2023-11-04 12:30:00',
                'translations' => ['en_US' => [
                    'label' => 'T-Shirts discount',
                ]],
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 6
                        ],
                    ],
                ],
                'actions' => [
                    [
                        'type' => FixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 1000,
                            ],
                        ],
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/post_promotion_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_a_promotion_without_required_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/promotions',
            server: $header,
            content: json_encode([], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/post_promotion_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }


    /** @test */
    public function it_does_not_create_a_promotion_with_taken_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/promotions',
            server: $header,
            content: json_encode([
                'name' => '50% Off on your first order',
                'code' => '50_off',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/post_promotion_with_taken_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_create_a_promotion_with_end_date_earlier_than_start_date(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/promotions',
            server: $header,
            content: json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'startsAt' => '2023-12-04 12:30:00',
                'endsAt' => '2023-11-04 12:30:00',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/post_promotion_with_invalid_dates_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            server: $header,
            content: json_encode([
                'name' => 'Christmas',
                'code' => 'new_code',
                'appliesToDiscounted' => true,
                'exclusive' => true,
                "usageLimit" => 11,
                "couponBased" => false,
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 1
                        ],
                    ],
                ],
                'actions' => [
                    [
                        'type' => FixedDiscountPromotionActionCommand::TYPE,
                        'configuration' => [
                            'WEB' => [
                                'amount' => 2,
                            ],
                        ],
                    ],
                ],
                'channels' => [
                    '/api/v2/admin/channels/MOBILE',
                ],
                'translations' => ['en_US' => [
                    '@id' => sprintf('/api/v2/admin/promotion-translations/%s', $promotion->getTranslation('en_US')->getId()),
                    'label' => 'Christmas',
                ]],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/put_promotion_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_promotion_with_null_priority(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            server: $header,
            content: json_encode([
                'priority' => null,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/put_promotion_with_null_priority_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_promotion(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_delete_the_promotion_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'promotion/promotion.yaml',
            'promotion/promotion_order.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_50_off'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/promotions/%s', $promotion->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
