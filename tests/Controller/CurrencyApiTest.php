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
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Axel Vankrunkelsven <axel@digilabs.be>
 */
class CurrencyApiTest extends JsonApiTestCase
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

    public function testCreateCurrencyAccessDeniedResponse()
    {
        $this->client->request('POST', '/api/currencies/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateCurrencyValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/currencies/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testCreateCurrencyResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "USD",
            "exchangeRate": 1,
            "enabled": true
        }
EOT;

        $this->client->request('POST', '/api/currencies/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'currency/create_response', Response::HTTP_CREATED);
    }

    public function testGetCurrenciesListAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/currencies/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetCurrenciesListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/currencies.yml');

        $this->client->request('GET', '/api/currencies/', [], [], static::$authorizedHeader);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/index_response', Response::HTTP_OK);
    }

    public function testGetCurrencyAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/currencies/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetCurrencyWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/currencies/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testGetCurrencyResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');

        $this->client->request('GET', '/api/currencies/'.$currencies['currency_1']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/show_response', Response::HTTP_OK);
    }

    public function testFullUpdateCurrencyAccessDeniedResponse()
    {
        $this->client->request('PUT', '/api/currencies/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testFullUpdateCurrencyWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/currencies/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testFullUpdateCurrencyValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');

        $this->client->request('PUT', '/api/currencies/'.$currencies['currency_2']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'currency/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testFullUpdateCurrencyResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');

        $data =
<<<EOT
        {
            "code": "EUR",
            "exchangeRate": 1.0000,
            "enabled": false
        }
EOT;

        $this->client->request('PUT', '/api/currencies/'.$currencies['currency_2']->getId(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/currencies/'.$currencies['currency_2']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/update_response', Response::HTTP_OK);
    }

    public function testPartialUpdateCurrencyWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/currencies/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testPartialUpdateCurrencyResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');

        $data =
<<<EOT
        {
            "exchangeRate": 1,
            "enabled": false
        }
EOT;

        $this->client->request('PATCH', '/api/currencies/'.$currencies['currency_2']->getId(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/currencies/'.$currencies['currency_2']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'currency/update_response', Response::HTTP_OK);
    }

    public function testDeleteCurrencyWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/currencies/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testDeleteCurrencyResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $currencies = $this->loadFixturesFromFile('resources/currencies.yml');

        $this->client->request('DELETE', '/api/currencies/'.$currencies['currency_1']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/currencies/'.$currencies['currency_1']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
