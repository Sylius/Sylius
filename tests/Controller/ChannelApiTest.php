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
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ChannelApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeader = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    public function testCreateChannelAccessDeniedResponse()
    {
        $this->client->request('POST', '/api/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateChannelValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/channels/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testCreateChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $data =
            <<<EOT
        {
            "code": "TABLET",
            "name": "Tablet Channel",
            "description": "Lorem ipsum",
            "hostname": "localhost",
            "color": "black",
            "enabled": true,
            "defaultLocale": "en_US",
            "defaultCurrency": "EUR",
            "taxCalculationStrategy": "order_items_based"
        }
EOT;

        $this->client->request('POST', '/api/channels/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel/create_response', Response::HTTP_CREATED);
    }

    public function testGetChannelsListAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetChannelsListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/channels/', [], [], static::$authorizedHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/index_response', Response::HTTP_OK);
    }

    public function testGetChannelAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/channels/achannel');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetChannelWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/channels/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testGetChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/channels/'.$channels['channel-web']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/show_response', Response::HTTP_OK);
    }

    public function testFullUpdateChannelAccessDeniedResponse()
    {
        $this->client->request('PUT', '/api/channels/achannel');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testFullUpdateChannelWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/channels/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testFullUpdateChannelValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('PUT', '/api/channels/'.$channels['channel-mobile']->getCode(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'channel/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testFullUpdateChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        $data =
            <<<EOT
                    {
            "code": "EUR",
            "exchangeRate": 1.0000,
            "enabled": false
        }
EOT;

        $this->client->request('PUT', '/api/channels/'.$channels['channel-mobile']->getCode(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/channels/'.$channels['channel-mobile']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/update_response', Response::HTTP_OK);
    }

    public function testPartialUpdateChannelWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/channels/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testPartialUpdateChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        $data =
            <<<EOT
                    {
            "exchangeRate": 1,
            "enabled": false
        }
EOT;

        $this->client->request('PATCH', '/api/channels/'.$channels['channel-mobile']->getCode(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/channels/'.$channels['channel-mobile']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/update_response', Response::HTTP_OK);
    }

    public function testDeleteChannelWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/channels/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testDeleteChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('DELETE', '/api/channels/'.$channels['channel-web']->getCode(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/channels/'.$channels['channel-web']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
