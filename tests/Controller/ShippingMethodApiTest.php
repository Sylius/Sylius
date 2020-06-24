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

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShippingMethodApiTest extends JsonApiTestCase
{
    /** @var array */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /** @var array */
    private static $authorizedHeaderWithDenied = [
        'HTTP_Authorization' => 'Bearer wrong_token',
        'HTTP_ACCEPT' => 'application/json',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_to_show_shipping_method_for_not_authenticated_users(): void
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->loadFixturesFromFiles([
            'resources/zones.yml',
            'resources/shipping_methods.yml',
        ])['ups'];

        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl($shippingMethod),
            [],
            [],
            static::$authorizedHeaderWithDenied
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_shipping_method_when_it_does_not_exist(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request(
            Request::METHOD_GET,
            '/api/v1/shipping-methods/-1',
            [],
            [],
            static::$authorizedHeaderWithAccept
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_shipping_method(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingMethods = $this->loadFixturesFromFiles([
            'resources/zones.yml',
            'resources/shipping_methods.yml',
        ]);
        $shippingMethod = $shippingMethods['ups'];

        $this->client->request(
            Request::METHOD_GET,
            $this->getShippingMethodUrl($shippingMethod),
            [],
            [],
            static::$authorizedHeaderWithAccept
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_method/show_response', Response::HTTP_OK);
    }

    private function getShippingMethodUrl(ShippingMethodInterface $shippingMethod): string
    {
        return '/api/v1/shipping-methods/' . $shippingMethod->getCode();
    }
}
