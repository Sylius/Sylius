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
class ProvinceApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    public function testGetProvinceAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/countries/1/provinces/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetProvinceResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countryData = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('GET', '/api/countries/'.$countryData['country_BE']->getId().'/provinces/'.$countryData['province_BE_limburg']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'province/show_response', Response::HTTP_OK);
    }

    public function testDeleteProvinceResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $countryData = $this->loadFixturesFromFile('resources/countries.yml');

        $this->client->request('DELETE', '/api/countries/'.$countryData['country_BE']->getId().'/provinces/'.$countryData['province_BE_limburg']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/countries/'.$countryData['country_BE']->getId().'/provinces/'.$countryData['province_BE_limburg']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
