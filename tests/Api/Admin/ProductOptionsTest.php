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

use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductOptionsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_does_not_update_a_product_option_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product_option.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()),
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        'name' => 'New Color',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_option/put_product_option_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_update_a_product_option_value_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product_option.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];
        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $fixtures['product_option_value_color_blue'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()),
            server: $header,
            content: json_encode([
                'values' => [
                    [
                        '@id' => sprintf('/api/v2/admin/product-option-values/%s', $productOptionValue->getCode()),
                        'translations' => [
                            'en_US' => [
                                'value' => 'Light Blue',
                            ],
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_option/put_product_option_value_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_a_product_option(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'product/product_option.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()),
            server: $header,
            content: json_encode([
                'values' => [
                    [
                        'code' => 'CHANGED_COLOR',
                        'translations' => [
                            'en_US' => [
                                'value' => 'Red',
                            ],
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_option/put_product_option_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_product_option(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-options',
            server: $header,
            content: json_encode([
                'code' => 'NEW_COLOR',
                'translations' => [
                    'en_US' => [
                        'name' => 'New Color',
                    ],
                ],
                'values' => [
                    [
                        'code' => 'NEW_COLOR_RED',
                        'translations' => [
                            'en_US' => [
                                'value' => 'Red',
                            ],
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_option/post_product_option_response',
            Response::HTTP_CREATED,
        );
    }
}
