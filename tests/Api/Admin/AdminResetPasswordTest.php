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
use Sylius\Tests\Api\Utils\ContentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminResetPasswordTest extends JsonApiTestCase
{
    /** @test */
    public function it_sends_administrator_password_reset_email(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->client->request(
            method: Request::METHOD_POST,
            uri: '/api/v2/admin/reset-password',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'api@example.com',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        self::assertEmailCount(1);
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_resets_admin_user_password_with_valid_token(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $validToken = 'valid_token';

        $this->client->request(
            method: Request::METHOD_PATCH,
            uri: sprintf('/api/v2/admin/reset-password/%s', $validToken),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }
}
