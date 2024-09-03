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

use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultPutHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_product_associations(): void
    {
        $this->loadFixturesFromFiles([
            'product/product_with_many_locales.yaml',
            'authentication/api_administrator.yaml',
        ]);

        $this->requestGet('/api/v2/admin/product-associations');

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/get_product_association_collection_response',
        );
    }

    /** @test */
    public function it_gets_a_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];

        $this->requestGet(sprintf('/api/v2/admin/product-associations/%s', $association->getId()));

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/get_product_association_response',
        );
    }

    /** @test */
    public function it_returns_nothing_if_association_not_found(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->requestGet('/api/v2/admin/product-associations/wrong_id');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_a_product_association(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/products_with_associations.yaml',
        ]);

        $this->requestPost(
            '/api/v2/admin/product-associations',
            [
                'type' => '/api/v2/admin/product-association-types/similar_products',
                'owner' => '/api/v2/admin/products/CUP',
                'associatedProducts' => [
                    '/api/v2/admin/products/MUG',
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/post_product_association_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_product_association_without_required_data(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/products_with_associations.yaml',
        ]);

        $this->requestPost('/api/v2/admin/product-associations', []);

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
            'authentication/api_administrator.yaml',
            'product/products_with_associations.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];

        $this->requestPost(
            '/api/v2/admin/product-associations',
            [
                'type' => sprintf('/api/v2/admin/product-association-types/%s', $association->getType()->getCode()),
                'owner' => sprintf('/api/v2/admin/products/%s', $association->getOwner()->getCode()),
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/post_duplicated_association_product_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_a_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/products_with_associations.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];

        $this->requestPut(
            sprintf('/api/v2/admin/product-associations/%s', $association->getId()),
            [
                'associatedProducts' => [
                    '/api/v2/admin/products/TANKARD',
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association/put_product_association_response',
        );
    }

    /** @test */
    public function it_deletes_a_product_association(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/products_with_associations.yaml',
        ]);

        /** @var ProductAssociationInterface $association */
        $association = $fixtures['product_association'];
        $associationId = $association->getId();

        $this->requestDelete(sprintf('/api/v2/admin/product-associations/%s', $associationId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);

        $this->requestGet(sprintf('/api/v2/admin/product-associations/%s', $associationId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
