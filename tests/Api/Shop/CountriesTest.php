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

final class CountriesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_countries(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'country.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/countries', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/country/get_countries_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_only_countries_from_current_channel(): void
    {
        $this->loadFixturesFromFiles(['channel/channel_with_countries.yaml', 'country.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/countries', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/country/get_countries_from_channel_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_a_country(): void
    {
        $this->loadFixturesFromFiles(['country.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/countries/US', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/country/get_country_response', Response::HTTP_OK);
    }
}
