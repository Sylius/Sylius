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
use Sylius\Component\Addressing\Model\CountryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jeroen Thora <jeroen.thora@intracto.com>
 */
final class CountryApiTest extends JsonApiTestCase
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
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_creating_country_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/countries/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_country_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/countries/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_country_with_given_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "BE"
        }
EOT;

        $this->client->request('POST', '/api/v1/countries/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_country_with_provinces()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "BE",
            "provinces": [
                {
                    "code": "BE-LM",
                    "name": "Limburg"
                }
            ]
        }
EOT;

        $this->client->request('POST', '/api/v1/countries/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_with_province_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_country_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/countries/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_countries_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/v1/countries/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_country()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');
        $country = $countries['country_NL'];

        $this->client->request('GET', $this->getCountryUrl($country), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_country_for_non_authenticated_user()
    {
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->client->request('GET', '/api/v1/countries/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_country_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/countries/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_country()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');
        $country = $countries['country_NL'];

        $this->client->request('DELETE', $this->getCountryUrl($country), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCountryUrl($country), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param CountryInterface $country
     *
     * @return string
     */
    private function getCountryUrl(CountryInterface $country)
    {
        return '/api/v1/countries/' . $country->getCode();
    }
}
