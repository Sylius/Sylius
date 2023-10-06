<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CurrenciesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_currencies(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'currency.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/currencies', server: self::CONTENT_TYPE_HEADER);

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/currency/get_currencies_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_a_currency(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'currency.yaml']);

        /** @var CurrencyInterface $currency */
        $currency = $fixtures['currency_usd'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/currencies/%s', $currency->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/currency/get_currency_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_cannot_get_currency_that_is_not_related_to_the_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'currency.yaml']);

        /** @var CurrencyInterface $currency */
        $currency = $fixtures['currency_gbp'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/currencies/%s', $currency->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
