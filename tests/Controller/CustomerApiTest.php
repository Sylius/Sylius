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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CustomerApiTest extends JsonApiTestCase
{

    /**
     * @test
     */
    public function it_denies_customer_creation_for_not_authenticated_users()
    {
        $this->client->request('POST', '/api/customers/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_customer_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/customers/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

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

        $this->client->request('POST', '/api/customers/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

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

        $this->client->request('POST', '/api/customers/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

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

        $this->client->request('POST', '/api/customers/', [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/create_with_user_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_access_to_customers_list_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/customers/');

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

        $this->client->request('GET', '/api/customers/', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_access_to_customer_details_for_not_authenticated_users()
    {
        $this->client->request('GET', '/api/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/customers/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

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

        $this->client->request('GET', '/api/customers/'.$customers['customer_Barry']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

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

        $this->client->request('GET', '/api/customers/'.$customers['customer_Roy']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/show_with_user_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_full_customer_update_for_not_authenticated_users()
    {
        $this->client->request('PUT', '/api/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_full_update_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/customers/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

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

        $this->client->request('PUT', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

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

        $this->client->request('PUT', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_partial_update_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/customers/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

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

        $this->client->request('PATCH', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'customer/partial_update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_customer_deletion_for_not_authenticated_users()
    {
        $this->client->request('DELETE', '/api/customers/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_deletion_of_a_customer_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/customers/-1', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

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

        $this->client->request('DELETE', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/customers/'.$customers['customer_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}
