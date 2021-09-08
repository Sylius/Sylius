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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CatalogPromotionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_catalog_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'catalog_promotion.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/catalog_promotion/get_catalog_promotions_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/catalog_promotion/get_catalog_promotion_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_a_catalog_promotion(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'product_variant.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header,
            json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'actions' => [
                    [
                        'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                        'configuration' => [
                            'amount' => 0.5
                        ]
                    ]
                ],
                'rules' => [
                    [
                        'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                        'configuration' => [
                            'MUG'
                        ],
                    ]
                ],
                'translations' => ['en_US' => [
                    'locale' => 'en_US',
                    'label' => 'T-Shirts discount',
                    'description' => '50% discount on every T-Shirt',
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/catalog_promotion/post_catalog_promotion_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_without_required_data(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header,
            json_encode([], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'admin/catalog_promotion/post_catalog_promotion_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_taken_code(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'catalog_promotion.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header,
            json_encode([
                'name' => 'Mugs discount',
                'code' => 'mugs_discount',
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'admin/catalog_promotion/post_catalog_promotion_with_taken_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /** @test */
    public function it_does_not_create_a_catalog_promotion_with_invalid_actions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'product_variant.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header,
            json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'tshirts_discount',
                'channels' => [
                    '/api/v2/admin/channels/WEB',
                ],
                'actions' => [
                    [
                        'type' => 'invalid_type',
                        'configuration' => [
                            'amount' => 0.5
                        ]
                    ],
                    [
                        'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                        'configuration' => []
                    ],
                    [
                        'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                        'configuration' => [
                            'amount' => 1.5
                        ]
                    ]
                ],
                'rules' => [
                    [
                        'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                        'configuration' => [
                            'MUG'
                        ],
                    ]
                ],
                'translations' => ['en_US' => [
                    'locale' => 'en_US',
                    'label' => 'T-Shirts discount',
                    'description' => '50% discount on every T-Shirt',
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse(
            $response,
            'admin/catalog_promotion/post_catalog_promotion_with_invalid_actions_response',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /** @test */
    public function it_updates_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();
        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            [],
            [],
            $header,
            json_encode([
                'name' => 'T-Shirts discount',
                'code' => 'new_code',
                'actions' => [
                    [
                        'type' => CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT,
                        'configuration' => [
                            'amount' => 0.4
                        ]
                    ]
                ],
                'rules' => [
                    [
                        'type' => CatalogPromotionRuleInterface::TYPE_FOR_VARIANTS,
                        'configuration' => [
                            'MUG'
                        ],
                    ]
                ],
                'channels' => [
                    '/api/v2/admin/channels/MOBILE',
                ],
                'translations' => ['en_US' => [
                    '@id' => sprintf('/api/v2/admin/catalog-promotion-translations/%s', $catalogPromotion->getTranslation('en_US')->getId()),
                    'label' => 'T-Shirts discount',
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/catalog_promotion/put_catalog_promotion_response', Response::HTTP_OK);
    }

    private function loadFixturesAndGetCatalogPromotion(): CatalogPromotionInterface
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'product_variant.yaml', 'catalog_promotion.yaml']);

        /** @var CatalogPromotionInterface $catalogPromotion */
        $catalogPromotion = $fixtures['catalog_promotion'];

        return $catalogPromotion;
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
