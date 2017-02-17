<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ShippingMethodApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_shipping_method_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/shipping-methods/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/zones.yml');
        $shippingMethods = $this->loadFixturesFromFile('resources/shipping_methods.yml');
        $shippingMethod = $shippingMethods['ups'];

        $this->client->request('GET', $this->getShippingMethodUrl($shippingMethod), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_method/show_response', Response::HTTP_OK);
    }

    /**
     * @param ShippingMethodInterface $shippingMethod
     *
     * @return string
     */
    private function getShippingMethodUrl(ShippingMethodInterface $shippingMethod)
    {
        return '/api/v1/shipping-methods/' . $shippingMethod->getCode();
    }
}
