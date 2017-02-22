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
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
final class CurrencyApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeader = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

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
    public function it_denies_creating_currency_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/currencies/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_currency_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/currencies/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_currency_with_given_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "USD"
        }
EOT;

        $this->client->request('POST', '/api/v1/currencies/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'currency/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_currencies_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/currencies/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_currencies_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/currencies.yml');

        $this->client->request('GET', '/api/v1/currencies/', [], [], static::$authorizedHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_currency_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/currencies/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_currency_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/currencies/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_currency()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');
        $currency = $currencies['currency_1'];

        $this->client->request('GET', $this->getCurrencyUrl($currency), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_currency_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/currencies/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_currency()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');
        $currency = $currencies['currency_1'];

        $this->client->request('DELETE', $this->getCurrencyUrl($currency), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCurrencyUrl($currency), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return string
     */
    private function getCurrencyUrl(CurrencyInterface $currency)
    {
        return '/api/v1/currencies/' . $currency->getCode();
    }
}
