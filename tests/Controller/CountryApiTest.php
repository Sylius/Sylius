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
 * @author Jeroen Thora <jeroen.thora@intracto.com>
 */
class CountryApiTest extends JsonApiTestCase
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
    public function test_create_country_access_denied_response()
    {
        $this->client->request('POST', '/api/countries/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function test_create_country_validation_fail_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/countries/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function test_create_country_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
            <<<EOT
        {
            "code": "BE"
        }
EOT;

        $this->client->request('POST', '/api/countries/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function test_get_s_country_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/countries/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_get_countries_list_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/countries/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_get_country_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/countries/'.$countries['country_NL']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'country/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function test_get_country_access_denied_response()
    {
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->client->request('GET', '/api/countries/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }


    /**
     * @test
     */
    public function test_delete_country_which_does_not_exist_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/countries/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function test_delete_country_response()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countries = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('DELETE', '/api/countries/' . $countries['country_NL']->getCode(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/countries/' . $countries['country_NL']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
