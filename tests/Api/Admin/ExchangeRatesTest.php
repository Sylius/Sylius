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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ExchangeRatesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_an_exchange_rate(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'exchange_rate.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_CNYUSD'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/exchange-rates/%s', $exchangeRate->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/exchange_rate/get_exchange_rate_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_exchange_rates(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'exchange_rate.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/exchange-rates',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/exchange_rate/get_exchange_rates_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_an_exchange_rate(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'exchange_rate.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/exchange-rates',
            server: $header,
            content: json_encode([
                'ratio' => 3.2,
                'sourceCurrency' => '/api/v2/admin/currencies/CNY',
                'targetCurrency' => '/api/v2/admin/currencies/PLN',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/exchange_rate/post_exchange_rate_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_exchange_rate(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'exchange_rate.yaml',
        ]);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_CNYUSD'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/exchange-rates/' . $exchangeRate->getId(),
            server: $header,
            content: json_encode([
                'ratio' => 0.25,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/exchange_rate/put_exchange_rate_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_an_exchange_rate(): void
    {
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'exchange_rate.yaml',
        ]);

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $fixtures['exchange_rate_USDPLN'];

        $this->requestDelete('/api/v2/admin/exchange-rates/' . $exchangeRate->getId());

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
