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
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Daniel Gorgan <danut007ro@gmail.com>
 */
final class OrderApiTest extends JsonApiTestCase
{
    /** @var array */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_access_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/orders/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_order_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/orders.yml');

        $this->client->request('POST', '/api/orders/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channelData = $this->loadFixturesFromFile('resources/orders.yml');

        $data =
<<<EOT
        {
            "channel": {$channelData['channel-web']->getId()},
            "currencyCode": "{$channelData['currency']->getCode()}"
        }
EOT;

        $this->client->request('POST', '/api/orders/', [], [], self::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/create_response', Response::HTTP_CREATED);
    }
}
