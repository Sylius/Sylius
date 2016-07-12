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
 * @author Joeri Timmermans <joeri.timmermans@intracto.com>
 * @group joeri
 */
class LocaleApiTest extends JsonApiTestCase
{

    public function testLocaleAccessDeniedResponse()
    {
        $this->client->request('POST', '/api/locales/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetLocalesListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('GET', '/api/locales/', [], [], [
            'HTTP_Authorization' => self::HTTP_AUTHORIZATION,
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/index_response', Response::HTTP_OK);
    }

    public function testGetLocaleResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $locales = $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('GET', '/api/locales/'.$locales['locale_en']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/show_response', Response::HTTP_OK);
    }

    public function testCreateLocaleValidationFailResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/locales/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    public function testCreateLocaleResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "es",
            "enabled": true
        }
EOT;

        $this->client->request('POST', '/api/locales/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'locale/create_response', Response::HTTP_CREATED);
    }

    public function testGetLocaleAccessDeniedResponse()
    {
        $this->client->request('GET', '/api/locales/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    public function testGetLocaleWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/locales/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
    
    public function testDeleteLocaleWhichDoesNotExistResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/locales/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    public function testDeleteLocaleResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $locales = $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('DELETE', '/api/locales/'.$locales['locale_en']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/locales/'.$locales['locale_en']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
