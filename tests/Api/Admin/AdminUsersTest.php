<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
            method: 'POST',
            uri: '/api/v2/admin/authentication-token',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'api@example.com',
                'password' => 'sylius',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/log_in_admin_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_sends_administrator_password_reset_email(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->client->request(
            method: Request::METHOD_POST,
            uri: '/api/v2/admin/reset-password-requests',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'api@example.com',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_resets_administrator_password(): void
    {
        $loadedData = $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        /** @var AdminUserInterface $adminUser */
        $adminUser = $loadedData['admin'];
        $adminUser->setPasswordResetToken('token');
        $adminUser->setPasswordRequestedAt(new \DateTime('now'));
        $this->getEntityManager()->flush();

        $this->client->request(
            method: 'PATCH',
            uri: '/api/v2/admin/reset-password-requests/token',
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_gets_administrators(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/administrators', server: $header);

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
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            server: $header,
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
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/administrators',
            server: $header,
            content: json_encode([
                'email' => 'j.api@test.com',
                'username' => 'johnApi',
                'plainPassword' => 'very-secure',
                'enabled' => true,
                'firstName' => 'John',
                'lastName' => 'Api',
                'localeCode' => 'ga_IE',
            ], \JSON_THROW_ON_ERROR),
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
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            server: $header,
            content: json_encode([
                'email' => 'j.api@test.com',
                'username' => 'johnApi',
                'plainPassword' => 'very-secure',
                'enabled' => false,
                'firstName' => 'John',
                'lastName' => 'Api',
                'localeCode' => 'ga_IE',
            ], \JSON_THROW_ON_ERROR),
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
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var AdminUserInterface $administrator */
        $administrator = $fixtures['admin_user_wilhelm'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/administrators/%s', $administrator->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
