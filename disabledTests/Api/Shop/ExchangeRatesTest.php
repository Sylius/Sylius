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

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeRatesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_exchange_rates(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'exchange_rate.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/exchange-rates', server: self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_exchange_rates_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_an_exchange_rate_with_source_currency_the_same_as_the_channel_base_currency(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'exchange_rate.yaml']);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_USDPLN'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/exchange-rates/%d', $exchangeRate->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_exchange_rate_usdpln_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_an_exchange_rate_with_target_currency_the_same_as_the_channel_base_currency(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'exchange_rate.yaml']);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_CNYUSD'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/exchange-rates/%d', $exchangeRate->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_exchange_rate_cnyusd_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_cannot_get_an_exchange_rate_that_is_not_related_to_the_channel_base_currency()
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'exchange_rate.yaml']);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_GBPBTN'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/exchange-rates/%d', $exchangeRate->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
