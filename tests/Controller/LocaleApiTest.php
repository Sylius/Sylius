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
 * @author Joeri Timmermans <joeri.timmermans@intracto.com>
 * @group joeri
 */
final class LocaleApiTest extends JsonApiTestCase
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
    public function it_denies_getting_locales_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/locales/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_locales_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('GET', '/api/v1/locales/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_locale()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $locales = $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('GET', '/api/v1/locales/'.$locales['locale_en_US']->getCode(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_locale_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/locales/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'locale/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_locale_with_given_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "es_ES"
        }
EOT;

        $this->client->request('POST', '/api/v1/locales/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'locale/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_locale_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/locales/en');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_locale_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/locales/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_locale_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/locales/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_locale()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $locales = $this->loadFixturesFromFile('resources/locales.yml');

        $this->client->request('DELETE', '/api/v1/locales/'.$locales['locale_en_US']->getCode(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/locales/'.$locales['locale_en_US']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
