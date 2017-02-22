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
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class TaxRateApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_tax_rates_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/tax_categories.yml');
        $this->loadFixturesFromFile('resources/zones.yml');
        $this->loadFixturesFromFile('resources/tax_rates.yml');

        $this->client->request('GET', '/api/v1/tax-rates/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_tax_rates()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');
        $this->loadFixturesFromFile('resources/zones.yml');
        $this->loadFixturesFromFile('resources/tax_rates.yml');

        $this->client->request('GET', '/api/v1/tax-rates/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_rate/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_tax_rate_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/tax-rates/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_tax_rate()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');
        $this->loadFixturesFromFile('resources/zones.yml');
        $taxRates = $this->loadFixturesFromFile('resources/tax_rates.yml');
        $taxRate = $taxRates['sales_tax'];

        $this->client->request('GET', $this->getTaxRateUrl($taxRate), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_rate/show_response', Response::HTTP_OK);
    }

    /**
     * @param TaxRateInterface $taxRate
     *
     * @return string
     */
    private function getTaxRateUrl(TaxRateInterface $taxRate)
    {
        return '/api/v1/tax-rates/' . $taxRate->getCode();
    }
}
