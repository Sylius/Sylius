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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OauthTokenApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_provides_an_access_token()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "client_id": "client_id",
            "client_secret": "secret",
            "grant_type": "password",
            "username": "api@sylius.com",
            "password": "sylius"
        }
EOT;

        $this->client->request('POST', '/api/oauth/v2/token', [], [], ['CONTENT_TYPE' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/new_access_token', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_refresh_an_access_token()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "client_id": "client_id",
            "client_secret": "secret",
            "grant_type": "refresh_token",
            "refresh_token": "SampleRefreshTokenODllODY4ZTQyOThlNWIyMjA1ZDhmZjE1ZDYyMGMwOTUxOWM2NGFmNGRjNjQ2NDBhMDVlNGZjMmQ0YzgyNDM2Ng"
        }
EOT;

        $this->client->request('POST', '/api/oauth/v2/token', [], [], ['CONTENT_TYPE' => 'application/json'], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/new_access_token', Response::HTTP_OK);
    }
}
