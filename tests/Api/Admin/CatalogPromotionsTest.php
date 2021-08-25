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

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CatalogPromotionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_allows_admin_to_get_catalog_promotions(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml']);
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
    public function it_allows_admin_to_get_catalog_promotion(): void
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
    public function it_allows_admin_to_create_a_catalog_promotion(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml']);
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
    public function it_allows_admin_to_update_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();
        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            [],
            [],
            $header,
            json_encode(['name' => 'new_promotion', 'code' => 'new_code'], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/catalog_promotion/put_catalog_promotion_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_admin_to_delete_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesAndGetCatalogPromotion();
        $header = $this->getLoggedHeader();

        $this->client->request(
            'DELETE',
            sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion->getCode()),
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    private function loadFixturesAndGetCatalogPromotion(): CatalogPromotionInterface
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml']);

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
