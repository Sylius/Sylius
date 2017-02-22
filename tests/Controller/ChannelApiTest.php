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
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_channels_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/channels.yml');

        $this->client->request('GET', '/api/v1/channels/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_channels()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');


        $this->client->request('GET', '/api/v1/channels/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_channel_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/channels/none');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_channel_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/channels/none', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_channel()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $channels = $this->loadFixturesFromFile('resources/channels.yml');
        /** @var ChannelInterface $channel */
        $channel = $channels['channel_web'];

        $this->client->request('GET', $this->getChannelUrl($channel), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'channel/show_response', Response::HTTP_OK);
    }

    /**
    * @param ChannelInterface $channel
     *
     * @return string
     */
    private function getChannelUrl(ChannelInterface $channel)
    {
         return '/api/v1/channels/' . $channel->getCode();
    }
}
