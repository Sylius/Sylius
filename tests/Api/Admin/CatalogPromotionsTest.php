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

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'GET',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_catalog_promotions_admin_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_admin_to_get_catalog_promotion(): void
    {
        $catalogPromotion = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml'])['catalog_promotion'];

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/catalog-promotions/%s', $catalogPromotion['id']),
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_catalog_promotion_admin_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_admin_to_post_new_catalog_promotion(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'catalog_promotion.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'POST',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header,
            json_encode(["name" => "new_promotion", "code" => "new_code"], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_CREATED);
    }

    /** @test */
    public function it_allows_admin_to_update_catalog_promotion(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $this->loadFixturesFromFile('catalog_promotion.yaml');

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        // Should be replaced with fixture_catalog_promotion['id'];
        $id = $this->getId($header);

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/catalog-promotions/%s', $id),
            [],
            [],
            $header,
            json_encode(["name" => "new_promotion", "code" => "new_code"], JSON_THROW_ON_ERROR)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/put_catalog_promotion_admin_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_admin_to_delete_catalog_promotion(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $catalogPromotion = $this->loadFixturesFromFile('catalog_promotion.yaml');

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        // Should be replaced with $catalogPromotion['id'];
        $id = $this->getId($header);

        $this->client->request(
            'DELETE',
            sprintf('/api/v2/admin/catalog-promotions/%s', $id),
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    private function getId(array $header): string
    {
        $this->client->request(
            'GET',
            '/api/v2/admin/catalog-promotions',
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);
        return (string) $content['hydra:member'][0]['id'];
    }
}
