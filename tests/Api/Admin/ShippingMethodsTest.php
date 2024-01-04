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

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/get_shipping_method_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_shipping_methods(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/shipping-methods', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/get_shipping_methods_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_archives_a_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/archive_shipping_method_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_restores_a_shipping_method(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()),
            server: $header,
        );
        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/shipping-methods/%s/restore', $shippingMethod->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/restore_shipping_method_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_update_a_shipping_method_with_duplicate_locale_translation(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'translations' => [
                    'en_US' => [
                        'name' => 'New UPS',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'translations[en_US].locale',
                    'message' => 'A translation for the "en_US" locale code already exists.',
                ],
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_update_shipping_methods_rules_with_wrong_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 'wrong_value',
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 'wrong_value',
                            ],
                            'WEB' => [
                                'amount' => 'wrong_value',
                            ],
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rules[0].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[1].configuration[weight]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[2].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[MOBILE][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'rules[3].configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_does_not_update_shipping_methods_rules_with_wrong_types(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'rules' => [
                    [
                        'type' => 'wrong_type',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'rules[0].type',
                    'message' => 'Invalid rule type. Available rule types are total_weight_greater_than_or_equal, total_weight_less_than_or_equal, order_total_greater_than_or_equal, order_total_less_than_or_equal.',
                ],
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_a_shipping_method_rules(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'rules' => [
                    [
                        'type' => 'total_weight_greater_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'total_weight_less_than_or_equal',
                        'configuration' => [
                            'weight' => 123,
                        ],
                    ],
                    [
                        'type' => 'order_total_greater_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                    [
                        'type' => 'order_total_less_than_or_equal',
                        'configuration' => [
                            'MOBILE' => [
                                'amount' => 123,
                            ],
                            'WEB' => [
                                'amount' => 123,
                            ],
                        ],
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/update_shipping_method_rules_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_update_shipping_method_calculator_configuration_with_wrong_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'calculator' => 'per_unit_rate',
                'configuration' => [
                    'WEB' => [
                        'amount' => 'wrong_value',
                    ],
                    'WRONG_CODE' => [
                        'amount' => 'wrong_value',
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponseViolations(
            $this->client->getResponse(),
            [
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be a valid number.',
                ],
                [
                    'propertyPath' => 'configuration[WEB][amount]',
                    'message' => 'This value should be of type numeric.',
                ],
                [
                    'propertyPath' => 'configuration[MOBILE]',
                    'message' => 'This field is missing.',
                ],
                [
                    'propertyPath' => 'configuration[WRONG_CODE]',
                    'message' => 'This field was not expected.',
                ],
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_updates_shipping_method_calculator_configuration(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            server: $header,
            content: json_encode([
                'calculator' => 'per_unit_rate',
                'calculatorConfiguration' => [
                    'WEB' => [
                        'amount' => 123,
                    ],
                    'MOBILE' => [
                        'amount' => 123,
                    ],
                ],
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/update_shipping_method_calculator_configuration_response',
            Response::HTTP_OK,
        );
    }
}
