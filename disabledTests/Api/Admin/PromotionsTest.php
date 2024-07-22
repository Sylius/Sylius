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
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ShippingCountryRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PromotionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

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
    public function it_gets_promotion_coupons(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'promotion/promotion.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var PromotionInterface $promotion */
        $promotion = $fixtures['promotion_1_off'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/promotions/%s/coupons', $promotion->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/get_promotion_coupons_response',
            Response::HTTP_OK,
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
                'usageLimit' => 11,
                'couponBased' => false,
                'rules' => [
                    [
                        'type' => CartQuantityRuleChecker::TYPE,
                        'configuration' => [
                            'count' => 1,
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
                            'MOBILE' => [
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
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/put_promotion_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_updates_promotion_to_last_priority_when_priority_is_minus_one(): void
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
                'priority' => -1,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/put_promotion_to_last_priority_when_priority_is_minus_one_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_update_a_promotion_with_duplicate_locale_translation(): void
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
                'translations' => ['en_US' => [
                    'label' => 'Christmas',
                ]],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/promotion/put_promotion_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
