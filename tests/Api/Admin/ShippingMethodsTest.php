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
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/put_shipping_method_with_duplicate_locale_translation',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }
}
