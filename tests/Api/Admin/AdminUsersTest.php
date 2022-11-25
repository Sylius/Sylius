<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminUsersTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_allows_admin_users_to_log_in(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->client->request(
            'POST',
            '/api/v2/admin/authentication-token',
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            json_encode([
                'email' => 'api@example.com',
                'password' => 'sylius'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/log_in_admin_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_sends_administrator_password_reset_email(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->client->request(
            Request::METHOD_POST,
            '/api/v2/admin/reset-password-requests',
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            json_encode([
                'email' => 'api@example.com',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_gets_administrators(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request('GET', '/api/v2/admin/administrators', [], [], $header,);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/administrator/get_administrators_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_an_administrator(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'administrator.yaml']);
        $header = $this->getLoggedHeader();

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/administrator/get_administrator_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_an_administrator(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/administrators',
            [],
            [],
            $header,
            json_encode([
                'email' => 'j.api@test.com',
                'username' => 'johnApi',
                'plainPassword' => 'very-secure',
                'enabled' => true,
                'firstName' => 'John',
                'lastName' => 'Api',
                'localeCode' => 'ga_IE',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/administrator/create_administrator_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_an_administrator(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'administrator.yaml']);
        $header = $this->getLoggedHeader();

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            [],
            [],
            $header,
            json_encode([
                'email' => 'j.api@test.com',
                'username' => 'johnApi',
                'plainPassword' => 'very-secure',
                'enabled' => false,
                'firstName' => 'John',
                'lastName' => 'Api',
                'localeCode' => 'ga_IE',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/administrator/put_administrator_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_an_administrator(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'administrator.yaml']);
        $header = $this->getLoggedHeader();

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            'DELETE',
            sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            [],
            [],
            $header
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
