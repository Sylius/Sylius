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
use Symfony\Component\HttpFoundation\Response;

final class ProductOptionsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpAdminContext();
    }

    /** @test */
    public function it_gets_a_product_option(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->requestGet(sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()));

        $this->assertResponseSuccessful('admin/product_option/get_product_option');
    }

    /** @test */
    public function it_gets_product_options(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        $this->requestGet('/api/v2/admin/product-options');

        $this->assertResponseSuccessful('admin/product_option/get_product_options');
    }

    /** @test */
    public function it_creates_a_product_option(): void
    {
        $this->setUpDefaultPostHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/product-options',
            body: [
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
                'position' => 9,
            ],
        );

        $this->assertResponseCreated('admin/product_option/post_product_option_response');
    }

    /** @test */
    public function it_does_not_allow_to_create_a_product_option_with_invalid_data(): void
    {
        $this->setUpDefaultPostHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
        ]);

        $this->requestPost(
            uri: '/api/v2/admin/product-options',
            body: [
                'values' => [
                    ['translations' => []],
                ],
            ],
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'code',
                    'message' => 'Please enter option code.',
                ],
                [
                    'propertyPath' => 'values[0].code',
                    'message' => 'Please enter option value code.',
                ],
                [
                    'propertyPath' => 'values[0].translations[en_US].value',
                    'message' => 'Please enter option value.',
                ],
                [
                    'propertyPath' => 'translations[en_US].name',
                    'message' => 'Please enter option name.',
                ],
            ],
        );
    }

    /** @test */
    public function it_updates_a_product_option(): void
    {
        $this->setUpDefaultPutHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()),
            body: [
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
                'position' => 9,
            ],
        );

        $this->assertResponseSuccessful('admin/product_option/put_product_option_response');
    }

    /** @test */
    public function it_does_not_update_a_product_option_with_duplicate_locale_translation(): void
    {
        $this->setUpDefaultPutHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->requestPut(
            uri: sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()),
            body: [
                'translations' => [
                    'en_US' => [
                        'name' => 'New Color',
                    ],
                ],
            ],
        );

        $this->assertResponseUnprocessableEntity(
            'admin/product_option/put_product_option_with_duplicate_locale_translation',
        );
    }

    /** @test */
    public function it_does_not_update_a_product_option_value_with_duplicate_locale_translation(): void
    {
        $this->setUpDefaultPutHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];
        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $fixtures['product_option_value_color_blue'];

        $productOptionCode = $productOption->getCode();

        $this->requestPut(
            uri: sprintf('/api/v2/admin/product-options/%s', $productOptionCode),
            body: [
                'values' => [
                    [
                        '@id' => sprintf('/api/v2/admin/product-options/%s/values/%s', $productOptionCode, $productOptionValue->getCode()),
                        'translations' => [
                            'en_US' => [
                                'value' => 'Light Blue',
                            ],
                        ],
                    ],
                ],
            ],
        );

        $this->assertResponseUnprocessableEntity('admin/product_option/put_product_option_value_with_duplicate_locale_translation');
    }

    /** @test */
    public function it_deletes_a_product_option(): void
    {
        $this->setUpDefaultDeleteHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        $this->requestDelete(sprintf('/api/v2/admin/product-options/%s', $productOption->getCode()));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
