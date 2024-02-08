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

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminUsersTest extends JsonApiTestCase
{
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
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }
}
