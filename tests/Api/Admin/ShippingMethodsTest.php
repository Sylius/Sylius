<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
        $header = $this->getLoggedHeader();

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/shipping-methods/%s', $shippingMethod->getCode()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        $this->client->request('GET', '/api/v2/admin/shipping-methods', [], [], $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/get_shipping_methods_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_archives_a_shipping_methods(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = $this->getLoggedHeader();

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            'PATCH',
            sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/archive_shipping_method_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_restores_a_shipping_methods(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);
        $header = $this->getLoggedHeader();

        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $fixtures['shipping_method_ups'];

        $this->client->request(
            'PATCH',
            sprintf('/api/v2/admin/shipping-methods/%s/archive', $shippingMethod->getCode()),
            [],
            [],
            $header,
        );
        $this->client->request(
            'PATCH',
            sprintf('/api/v2/admin/shipping-methods/%s/restore', $shippingMethod->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/shipping_method/restore_shipping_method_response',
            Response::HTTP_OK,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
