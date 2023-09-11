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

use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductAttributesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_product_attributes(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product_attribute.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-attributes', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_attribute/get_product_attributes_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_product_attribute(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_attribute.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $fixtures['product_attribute_text_delete'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-attributes/%s', $productAttribute->getCode()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_creates_a_product_attribute(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-attributes',
            server: $header,
            content: json_encode([
                'code' => 'material',
                'configuration' => [
                    'choices' => [
                        '0afb212e-cd08-11ec-871e-0242ac120005' => [
                            'en_US' => 'Cotton',
                            'pl_PL' => 'Bawelna',
                        ],
                        '0afb4e88-cd08-11ec-bcd4-0242ac120005' => [
                            'en_US' => 'Wool',
                        ],
                    ],
                    'multiple' => true,
                    'min' => 1,
                    'max' => 3,
                ],
                'type' => SelectAttributeType::TYPE,
                'translatable' => true,
                'translations' => [
                    'en_US' => [
                        'locale' => 'en_US',
                        'name' => 'Material',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_attribute/post_product_attribute_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_no_create_a_product_attribute_without_required_data(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-attributes',
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        'locale' => 'en_US',
                    ],
                ],
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_attribute/post_product_attribute_without_required_data_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_no_create_a_product_attribute_with_unregistered_type(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-attributes',
            server: $header,
            content: json_encode([
                'code' => 'test',
                'type' => 'foobar',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_attribute/post_product_attribute_with_unregistered_type_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_a_product_attribute(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'product/product_attribute.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $fixtures['product_attribute_select'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/product-attributes/' . $productAttribute->getCode(),
            server: $header,
            content: json_encode([
                'configuration' => [
                    'choices' => [
                        '0afb212e-cd08-11ec-871e-0242ac120005' => [
                            'en_US' => 'handmade',
                            'fr_FR' => 'fait la main',
                        ],
                        '0afb4e88-cd08-11ec-bcd4-0242ac120005' => [
                            'fr_FR' =>  'coffret cadeau',
                            'en_US' => 'gift wrapping',
                            'pl_PL' => 'pakowanie na prezent',
                        ],
                        '0afb4e44-cd08-11ec-ad3f-0242ac120005' => [
                            'en_US' => 'eco approved',
                        ],
                    ],
                    'min' => 1,
                    'max' => 3,
                    'multiple' => true,
                ],
                'translatable' => true,
                'position' => 0,
                'translations' => [
                    'en_US' => [
                        '@id' => sprintf(
                            '/api/v2/admin/product-attribute-translations/%s',
                            $productAttribute->getTranslation('en_US')->getId(),
                        ),
                        'locale' => 'en_US',
                        'name' => 'Additional information',
                    ],
                    'pl_PL' => [
                        'locale' => 'pl_PL',
                        'name' => 'Dodatkowe informacje',
                    ],
                ]
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_attribute/put_product_attribute_response',
            Response::HTTP_OK,
        );
    }
}
