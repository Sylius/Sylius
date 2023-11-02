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

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/products/%s', $product->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/get_product_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_products(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml', 'product/product.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/products',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/get_products_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_product(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'taxonomy.yaml',
            'product/product_option.yaml',
            'product/product_attribute.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/products',
            server: $header,
            content: json_encode([
                'code' => 'MUG',
                'variantSelectionMethod' => ProductInterface::VARIANT_SELECTION_MATCH,
                'enabled' => true,
                'options' => [
                    '/api/v2/admin/product-options/COLOR',
                ],
                'mainTaxon' => '/api/v2/admin/taxons/MUG',
                'channels' => [
                    '/api/v2/admin/channels/WEB_GB',
                ],
                'attributes' => [[
                    'attribute' => '/api/v2/admin/product-attributes/dishwasher_safe',
                    'value' => true,
                ]],
                'translations' => [
                    'en_US' => [
                        'locale' => 'en_US',
                        'slug' => 'mug',
                        'name' => 'Mug',
                        'description' => 'This is a mug',
                        'shortDescription' => 'Short mug description',
                        'metaKeywords' => 'mug',
                        'metaDescription' => 'Mug description',
                    ],
                    'pl_PL' => [
                        'locale' => 'pl_PL',
                        'slug' => 'kubek',
                        'name' => 'Kubek',
                        'description' => 'To jest kubek',
                        'shortDescription' => 'Krótki opis kubka',
                        'metaKeywords' => 'kubek',
                        'metaDescription' => 'Opis kubka',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/post_product_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_the_existing_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product.yaml',
            'product/product_attribute.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/products/%s', $product->getCode()),
            server: $header,
            content: json_encode([
                'enabled' => false,
                'mainTaxon' => '/api/v2/admin/taxons/CAPS',
                'channels' => [
                    '/api/v2/admin/channels/MOBILE',
                ],
                'attributes' => [
                    [
                        '@id' => sprintf(
                            '/api/v2/admin/product-attribute-values/%s',
                            $product->getAttributeByCodeAndLocale('MATERIAL', 'en_US')->getId()
                        ),
                        'attribute' => '/api/v2/admin/product-attributes/MATERIAL',
                        'value' => 'Cotton',
                        'localeCode' => 'en_US',
                    ],
                    [
                        '@id' => sprintf(
                            '/api/v2/admin/product-attribute-values/%s',
                            $product->getAttributeByCodeAndLocale('MATERIAL', 'pl_PL')->getId()
                        ),
                        'attribute' => '/api/v2/admin/product-attributes/MATERIAL',
                        'value' => 'Bawełna',
                        'localeCode' => 'pl_PL',
                    ],
                    [
                        'attribute' => '/api/v2/admin/product-attributes/dishwasher_safe',
                        'value' => true,
                    ]
                ],
                'translations' => [
                    'en_US' => [
                        '@id' => sprintf('/api/v2/admin/product-translations/%s', $product->getTranslation('en_US')->getId()),
                        'locale' => 'en_US',
                        'slug' => 'caps/cap',
                        'name' => 'Cap',
                        'description' => 'This is a cap',
                        'shortDescription' => 'Short cap description',
                        'metaKeywords' => 'cap',
                        'metaDescription' => 'Cap description',
                    ],
                    'pl_PL' => [
                        '@id' => sprintf('/api/v2/admin/product-translations/%s', $product->getTranslation('pl_PL')->getId()),
                        'locale' => 'pl_PL',
                        'slug' => 'czapki/czapka',
                        'name' => 'Czapka',
                        'description' => 'To jest czapka',
                        'shortDescription' => 'Krótki opis czapki',
                        'metaKeywords' => 'czapka',
                        'metaDescription' => 'Opis czapki',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/put_product_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_the_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_socks'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/products/%s', $product->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_delete_the_product_in_use(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_cap'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/products/%s', $product->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/delete_product_in_use_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
