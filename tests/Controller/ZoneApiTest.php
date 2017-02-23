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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ZoneApiTest extends JsonApiTestCase
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
    public function it_denies_zone_creation_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/zones/country');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_zone_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/zones/country', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_zone_with_members()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');

        $data =
<<<EOT
        {
            "code": "EU",
            "name": "European Union",
            "scope": "all",
            "members": [
                {
                    "code": "NL"
                },
                {
                    "code": "BE"
                }
            ]
        }
EOT;

        $this->client->request('POST', '/api/v1/zones/country', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_access_to_zones_list_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/v1/zones/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_zones_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/zones.yml');

        $this->client->request('GET', '/api/v1/zones/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_access_to_zone_details_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/v1/zones/azone');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_zone_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/zones/azone', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_zone_details()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $zones = $this->loadFixturesFromFile('resources/zones.yml');

        $this->client->request('GET', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_zone_full_update_for_not_authenticated_users()
    {
        $this->client->request('PUT', '/api/v1/zones/azone');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_full_update_of_a_zone_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/zones/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_zone_fully_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $zones = $this->loadFixturesFromFile('resources/zones.yml');

        $this->client->request('PUT', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_zone_fully()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $zones = $this->loadFixturesFromFile('resources/zones.yml');

        $data =
<<<EOT
        {
            "name": "European Union +",
            "scope": "all",
            "members": [
                {
                    "code": "PL"
                }
            ]
        }
EOT;

        $this->client->request('PUT', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_zone_partial_update_for_not_authenticated_users()
    {
        $this->client->request('PATCH', '/api/v1/zones/azone');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_partial_update_of_a_zone_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/zones/azone', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_update_zone_partially()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $zones = $this->loadFixturesFromFile('resources/zones.yml');

        $data =
<<<EOT
        {
            "name": "European Union +"
        }
EOT;

        $this->client->request('PATCH', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'zone/update_partially_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_zone_delete_for_not_authenticated_users()
    {
        $this->client->request('DELETE', '/api/v1/zones/azone');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_delete_of_a_zone_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/zones/azone', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_zone()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $zones = $this->loadFixturesFromFile('resources/zones.yml');

        $this->client->request('DELETE', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/zones/'.$zones['zone_eu']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
