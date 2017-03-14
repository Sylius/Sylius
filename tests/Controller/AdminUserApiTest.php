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
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AdminUserApiTest extends JsonApiTestCase
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
    public function it_denies_an_admin_user_creation_for_not_authenticated_users()
    {
        $this->client->request('POST', '/api/v1/users/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_an_admin_user_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/users/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_an_admin_user()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "username": "Barlog",
            "email": "teamEvil@middleearth.com",
            "plainPassword": "youShallNotPass",
            "localeCode": "en_US"
        }
EOT;

        $this->client->request('POST', '/api/v1/users/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_an_admin_user_with_not_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "firstName": "Orange",
            "lastName": "Annoying",
            "username": "Nenene",
            "email": "orange@fruits.com",
            "plainPassword": "hejPear!",
            "localeCode": "en_US",
            "enabled": "true"
        }
EOT;

        $this->client->request('POST', '/api/v1/users/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/create_with_additional_fields_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_access_to_an_admin_users_list_for_not_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/users/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_an_admin_users_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/admin_users.yml');

        $this->client->request('GET', '/api/v1/users/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_access_to_an_admin_user_details_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/v1/users/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_admin_user_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/users/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_shows_an_admin_user_details()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/admin_users.yml');

        $this->client->request('GET', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_an_admin_user_full_update_for_not_authenticated_users()
    {
        $this->client->request('PUT', '/api/v1/users/1');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_full_update_of_an_admin_user_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/users/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_an_admin_user_fully_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/admin_users.yml');

        $this->client->request('PUT', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_an_admin_user_fully()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/admin_users.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "firstName": "Orange",
            "lastName": "Annoying",
            "username": "Nenene",
            "email": "orange@fruits.com",
            "plainPassword": "hejPear!",
            "localeCode": "fr_FR",
            "enabled": "true"
        }
EOT;

        $this->client->request('PUT', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_partial_update_of_an_admin_user_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/users/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_update_an_admin_user_partially()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/admin_users.yml');

        $data =
<<<EOT
        {
            "firstName": "John",
            "lastName": "Doe"
        }
EOT;

        $this->client->request('PATCH', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/partial_update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_an_admin_user_deletion_for_not_authenticated_users()
    {
        $this->client->request('DELETE', '/api/v1/users/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_deletion_of_an_admin_user_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/users/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_an_admin_user()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/admin_users.yml');

        $this->client->request('DELETE', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getAdminUserUrl($users['admin1']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_current_logged_an_admin_user()
    {
        $user = $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/admin_users.yml');

        $this->client->request('DELETE', $this->getAdminUserUrl($user['admin']), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'admin_user/deletion_fail_response',  Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param AdminUserInterface $user
     *
     * @return string
     */
    private function getAdminUserUrl(AdminUserInterface $user)
    {
        return '/api/v1/users/' . $user->getId();
    }
}
