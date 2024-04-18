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

namespace Sylius\Tests\Api\Shop;

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ChannelsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_collection_with_single_default_channel(): void
    {
        $this->loadFixturesFromFile('channel.yaml');

        $this->client->request(method: 'GET', uri: '/api/v2/shop/channels', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/channel/get_channels_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_error_if_no_channel_found(): void
    {
        $this->client->request(method: 'GET', uri: '/api/v2/shop/channels', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_channel_by_code(): void
    {
        $this->loadFixturesFromFile('channel.yaml');

        $this->client->request(method: 'GET', uri: '/api/v2/shop/channels/WEB', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/channel/get_channel_by_code_response', Response::HTTP_OK);
    }
}
