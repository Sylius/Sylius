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

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jeroen Thora <jeroen.thora@intracto.com>
 */
class CountryApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */

    public function testCreateCountryAccessDeniedResponse()
    {
        $this->client->request('POST', '/api/countries/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateCountryValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/countries/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testCreateCountryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
            <<<EOT
        {
            "code": "BE"
        }
EOT;

        $this->client->request('POST', '/api/countries/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_response', Response::HTTP_CREATED);
    }

    public function testGetSCountryWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/countries/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testGetCountriesListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/countries/', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/index_response', Response::HTTP_OK);
    }

    public function testGetCountryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/countries/'.$countries['country_NL']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/show_response', Response::HTTP_OK);
    }

    public function testGetCountryAccessDeniedResponse()
    {
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->client->request('GET', '/api/countries/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }


    public function testDeleteCountryWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/countries/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testDeleteCountryResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('DELETE', '/api/countries/' . $countries['country_NL']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/countries/' . $countries['country_NL']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
