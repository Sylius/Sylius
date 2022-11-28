<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CurrenciesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_country(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml']);
        $header = $this->getLoggedHeader();

        /** @var CurrencyInterface $currency */
        $currency = $fixtures['currency_gbp'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/currencies/%s', $currency->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/get_currency_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_currencies(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/currencies',
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/get_currencies_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_currency(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/currencies',
            [],
            [],
            $header,
            json_encode([
                'code' => 'KRW',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/currency/post_currency_response',
            Response::HTTP_CREATED,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
