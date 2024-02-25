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

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CurrenciesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_a_currency(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['currency.yaml']);

        /** @var CurrencyInterface $currency */
        $currency = $fixtures['currency_gbp'];

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
}
