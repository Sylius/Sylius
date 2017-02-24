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
final class CustomerApiTest extends JsonApiTestCase
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
    public function it_denies_customer_creation_for_not_authenticated_users()
    {
        $this->client->request('POST', '/api/v1/customers/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_customer_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/customers/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_customer_with_user_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "user": {
                "enabled": "true"
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/customers/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/create_with_user_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_customer_without_user_account()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "firstName": "John",
            "lastName": "Diggle",
            "email": "john.diggle@yahoo.com",
            "gender": "m"
        }
EOT;

        $this->client->request('POST', '/api/v1/customers/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_customer_with_user_account()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "firstName": "John",
            "lastName": "Diggle",
            "email": "john.diggle@yahoo.com",
            "gender": "m",
            "user": {
                "plainPassword" : "testPassword"
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/customers/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/create_with_user_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_access_to_customers_list_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/v1/customers/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_customers_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('GET', '/api/v1/customers/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_access_to_customer_details_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/v1/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/customers/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_only_customer_details_if_no_user_account_is_connected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('GET', '/api/v1/customers/'.$customers['customer_Barry']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_customer_and_user_details()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('GET', '/api/v1/customers/'.$customers['customer_Roy']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/show_with_user_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_full_customer_update_for_not_authenticated_users()
    {
        $this->client->request('PUT', '/api/v1/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_full_update_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/customers/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_customer_fully_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('PUT', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_customer_fully()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $data =
<<<EOT
        {
            "firstName": "John",
            "lastName": "Diggle",
            "email": "john.diggle@example.com",
            "gender": "m"
        }
EOT;

        $this->client->request('PUT', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_partial_update_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/customers/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_update_customer_partially()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $data =
<<<EOT
        {
            "firstName": "John",
            "lastName": "Doe"
        }
EOT;

        $this->client->request('PATCH', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/partial_update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_customer_deletion_for_not_authenticated_users()
    {
        $this->client->request('DELETE', '/api/v1/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_deletion_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/customers/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_customer()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('DELETE', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/customers/'.$customers['customer_Oliver']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
