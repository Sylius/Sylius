<?php

namespace Sylius\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class UserApiTest extends JsonApiTestCase
{
    public function testGetUsersListResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('GET', '/api/users/', [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'user/index_response', Response::HTTP_OK);
    }

    public function testShowUserResponse()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $users = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('GET', '/api/users/' . $users['user_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'user/show_response', Response::HTTP_OK);
    }

    public function testDeleteUser()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $customers = $this->loadFixturesFromFile('resources/customers.yml');

        $this->client->request('DELETE', '/api/users/'.$customers['user_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_CONTENT_TYPE);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/users/'.$customers['user_Oliver']->getId(), [], [], self::AUTHORIZATION_HEADER_WITH_ACCEPT);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }
}