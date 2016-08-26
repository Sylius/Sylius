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
final class ChannelApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    public function testGetChannelAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetChannelResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channelData = $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/channels/'.$channelData['channel-web']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/show_response', Response::HTTP_OK);
    }
}
