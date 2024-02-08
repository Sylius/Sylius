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

use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationTypesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_product_association_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_with_many_locales.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        /** @var ProductAssociationTypeInterface $associationType */
        $associationType = $fixtures['product_association_type'];
        $this->client->request('GET',
            sprintf('/api/v2/admin/product-association-types/%s', $associationType->getCode()),
            [],
            [],
            $header
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_product_association_type_response',
            Response::HTTP_OK
        );
    }

    /** @test */
    public function it_returns_nothing_if_association_type_not_found(): void
    {
        $this->loadFixturesFromFiles(['product/product_with_many_locales.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request('GET',
            '/api/v2/admin/product-association-types/wrong input',
            [],
            [],
            $header
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_product_association_type_collection(): void
    {
        $this->loadFixturesFromFiles(['product/product_with_many_locales.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request('GET',
            '/api/v2/admin/product-association-types',
            [],
            [],
            $header
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_product_association_type_collection_response',
            Response::HTTP_OK
        );
    }

    /** @test */
    public function it_creates_product_association_type(): void
    {
        $this->loadFixturesFromFiles(['product/product_with_many_locales.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'POST',
            '/api/v2/admin/product-association-types',
            [],
            [],
            $header,
            json_encode([
                'code' => 'TEST',
                'translations' => ['en_US' => [
                    'name' => 'test',
                    'description' => 'test description',
                    'locale' => 'en_US'
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_product_association_type_response',
            Response::HTTP_CREATED
        );
    }

    /** @test */
    public function it_updates_product_association_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['product/product_with_many_locales.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        /** @var ProductAssociationTypeInterface $associationType */
        $associationType = $fixtures['product_association_type'];
        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/product-association-types/%s', $associationType->getCode()),
            [],
            [],
            $header,
            json_encode([
                'code' => 'TEST',
                'translations' => ['en_US' => [
                    'name' => 'test',
                    'description' => 'test description',
                    'locale' => 'de_DE'
                ]]
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/put_product_association_type_response',
            Response::HTTP_OK
        );
    }
}
