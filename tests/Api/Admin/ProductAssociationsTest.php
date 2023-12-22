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

namespace Api\Admin;

use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-associations/%s', $association->getId()),
            server: $this
                ->headerBuilder()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/get_product_association_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_nothing_if_association_not_found(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-associations/nope',
            server: $this
                ->headerBuilder()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
        );

        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_product_association_collection(): void
    {
        $this->loadFixturesFromFiles([
            'product/product_with_many_locales.yaml',
            'authentication/api_administrator.yaml'
        ]);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/product-associations',
            server: $this
                ->headerBuilder()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/get_product_association_collection_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_product_association(): void
    {
        $this->loadFixturesFromFiles([
            'product/products_with_associations.yaml',
            'authentication/api_administrator.yaml',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-associations',
            server: $this
                ->headerBuilder()
                ->withJsonLdContentType()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
            content: json_encode([
                'type' => '/api/v2/admin/product-association-types/similar_products',
                'owner' => '/api/v2/admin/products/CUP',
                'associatedProducts' => [
                    '/api/v2/admin/products/MUG',
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/post_product_association_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/products_with_associations.yaml',
            'authentication/api_administrator.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];
        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-associations/%s', $association->getId()),
            server: $this
                ->headerBuilder()
                ->withJsonLdContentType()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
            content: json_encode([
                'associatedProducts' => [
                    '/api/v2/admin/products/TANKARD',
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/put_product_association_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/products_with_associations.yaml',
            'authentication/api_administrator.yaml',
        ]);

        $header = $this
            ->headerBuilder()
            ->withJsonLdAccept()
            ->withAdminUserAuthorization('api@example.com')
            ->build()
        ;

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];
        $associationId = $association->getId();

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-associations/%s', $associationId),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-associations/%s', $associationId),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_does_not_create_product_association_without_required_data(): void
    {
        $this->loadFixturesFromFiles([
            'product/products_with_associations.yaml',
            'authentication/api_administrator.yaml',
        ]);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-associations',
            server: $this
                ->headerBuilder()
                ->withJsonLdContentType()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
            content: '{}',
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/post_product_association_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_create_duplicated_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/products_with_associations.yaml',
            'authentication/api_administrator.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-associations',
            server: $this
                ->headerBuilder()
                ->withJsonLdContentType()
                ->withJsonLdAccept()
                ->withAdminUserAuthorization('api@example.com')
                ->build()
            ,
            content: json_encode([
                'type' => sprintf('/api/v2/admin/product-association-types/%s', $association->getType()->getCode()),
                'owner' => sprintf('/api/v2/admin/products/%s', $association->getOwner()->getCode()),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/post_duplicated_association_product_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
