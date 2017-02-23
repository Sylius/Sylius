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
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ExchangeRateApiTest extends JsonApiTestCase
{
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
        'Accept' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_creating_exchange_rate_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/exchange-rates/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_exchange_rate_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/exchange-rates/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_exchange_rate()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/currencies.yml');

        $data =
<<<EOT
        {
            "ratio": "0,8515706",
            "sourceCurrency": "EUR",
            "targetCurrency": "GBP"
        }
EOT;

        $this->client->request('POST', '/api/v1/exchange-rates/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_exchange_rate_with_duplicated_currency_pair()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/exchange_rates.yml');

        $data =
<<<EOT
        {
            "ratio": "0,8515706",
            "sourceCurrency": "EUR",
            "targetCurrency": "GBP"
        }
EOT;

        $this->client->request('POST', '/api/v1/exchange-rates/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/non_unique_pair_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_denies_getting_exchange_rates_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/exchange-rates/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_exchange_rates_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/exchange_rates.yml');

        $this->client->request('GET', '/api/v1/exchange-rates/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_exchange_rate_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/exchange-rates/EUR-USD');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_exchange_rate_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/exchange-rates/EUR-USD', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_exchange_rate()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_gbp_exchange_rate'];

        $this->client->request('GET', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_updating_exchange_rate_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/exchange-rates/EUR-USD');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_updating_exchange_rate_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/exchange-rates/EUR-USD', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_exchange_rate_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_usd_exchange_rate'];

        $this->client->request('PUT', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_exchange_rate()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_usd_exchange_rate'];

        $data =
<<<EOT
        {
            "ratio": 0.84
        }
EOT;

        $this->client->request('PUT', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/update_response', Response::HTTP_OK);
    }


    /**
     * @test
     */
    public function it_does_not_allow_to_update_exchange_rates_currencies()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_usd_exchange_rate'];

        $data =
<<<EOT
        {
            "ratio": 0.84,
            "sourceCurrency": "GBP",
            "targetCurrency": "EUR"
        }
EOT;

        $this->client->request('PATCH', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_exchange_rate_if_ratio_is_not_a_number()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_usd_exchange_rate'];

        $data =
<<<EOT
        {
            "ratio": "its-a-trap"
        }
EOT;

        $this->client->request('PUT', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'exchange_rate/update_ratio_with_string_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_exchange_rate_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/exchange-rates/EUR-USD', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_exchange_rate()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $exchangeRates = $this->loadFixturesFromFile('resources/exchange_rates.yml');

        /** @var ExchangeRateInterface $exchangeRate */
        $exchangeRate = $exchangeRates['eur_gbp_exchange_rate'];

        $this->client->request('DELETE', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getExchangeRateUrl($exchangeRate), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param ExchangeRateInterface $exchangeRate
     *
     * @return string
     */
    private function getExchangeRateUrl(ExchangeRateInterface $exchangeRate)
    {
        return sprintf('/api/v1/exchange-rates/%s-%s', $exchangeRate->getSourceCurrency()->getCode(), $exchangeRate->getTargetCurrency()->getCode());
    }
}
