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
use Symfony\Component\HttpFoundation\Response;

final class ProductAssociationTypesTest extends JsonApiTestCase
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
    public function it_gets_product_association_types(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->requestGet('/api/v2/admin/product-association-types');

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/get_product_association_types_response',
        );
    }

    /** @test */
    public function it_gets_a_product_association_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'product/product_with_many_locales.yaml',
            'authentication/api_administrator.yaml',
        ]);

        /** @var ProductAssociationTypeInterface $associationType */
        $associationType = $fixtures['product_association_type'];

        $this->requestGet(sprintf('/api/v2/admin/product-association-types/%s', $associationType->getCode()));

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/get_product_association_type_response',
        );
    }

    /** @test */
    public function it_returns_nothing_if_association_type_not_found(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->requestGet('/api/v2/admin/product-association-types/wrong_input');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_creates_a_product_association_type(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->requestPost(
            '/api/v2/admin/product-association-types',
            [
                'code' => 'TEST',
                'translations' => ['en_US' => [
                    'name' => 'test',
                    'description' => 'test description',
                ]],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/post_product_association_type_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_product_association_type_without_required_data(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        $this->requestPost('/api/v2/admin/product-association-types', []);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/post_product_association_type_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_a_product_association_type(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        /** @var ProductAssociationTypeInterface $associationType */
        $associationType = $fixtures['product_association_type'];

        $this->requestPut(
            sprintf('/api/v2/admin/product-association-types/%s', $associationType->getCode()),
            [
                'code' => 'TEST',
                'translations' => [
                    'en_US' => [
                        '@id' => sprintf(
                            '/api/v2/admin/product-association-types/%s/translations/en_US',
                            $associationType->getCode(),
                        ),
                        'name' => 'Similar products',
                    ],
                    'de_DE' => [
                        'name' => 'test',
                        'description' => 'test description',
                    ],
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/put_product_association_type_response',
        );
    }

    /** @test */
    public function it_does_not_update_a_product_association_type_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_with_many_locales.yaml',
        ]);

        /** @var ProductAssociationTypeInterface $associationType */
        $associationType = $fixtures['product_association_type'];

        $this->requestPut(
            sprintf('/api/v2/admin/product-association-types/%s', $associationType->getCode()),
            [
                'code' => 'TEST',
                'translations' => [
                    'en_US' => [
                        'name' => 'Similar products',
                    ],
                ],
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_association_type/put_product_association_type_with_duplicate_locale_translation_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
