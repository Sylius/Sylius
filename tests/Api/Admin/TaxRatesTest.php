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

use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxRatesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_tax_rate(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_rates.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxRateInterface $taxRate */
        $taxRate = $fixtures['regular_tax'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/tax-rates/%s', $taxRate->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_rate/get_tax_rate_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_tax_rates(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_rates.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/tax-rates',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_rate/get_tax_rates_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_new_tax_rate(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_rates.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/tax-rates',
            server: $header,
            content: json_encode([
                'code' => 'unregular_tax',
                'zone' => '/api/v2/admin/zones/EU',
                'category' => '/api/v2/admin/tax-categories/TC1',
                'name' => 'Unregular Tax 90%',
                'amount' => 0.9,
                'includedInPrice' => true,
                'calculator' => 'default',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_rate/post_tax_rate_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_creates_a_new_tax_rate_with_default_amount(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_rates.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/tax-rates',
            server: $header,
            content: json_encode([
                'code' => 'unregular_tax',
                'zone' => '/api/v2/admin/zones/EU',
                'category' => '/api/v2/admin/tax-categories/TC1',
                'name' => 'Unregular Tax 90%',
                'includedInPrice' => true,
                'calculator' => 'default',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_rate/post_tax_rate_with_no_amount_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_existing_tax_rate(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'tax_rates.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var TaxRateInterface $taxRate */
        $taxRate = $fixtures['regular_tax'];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/tax-rates/' . $taxRate->getCode(),
            server: $header,
            content: json_encode([
                'zone' => '/api/v2/admin/zones/EU',
                'category' => '/api/v2/admin/tax-categories/TC2',
                'name' => 'Regular Tax 30%',
                'amount' => 0.3,
                'includedInPrice' => true,
                'calculator' => 'default',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/tax_rate/put_tax_rate_response',
            Response::HTTP_OK,
        );
    }
}
