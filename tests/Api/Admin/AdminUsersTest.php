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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminUsersTest extends JsonApiTestCase
{
    /** @test */
    public function it_sends_administrator_password_reset_email(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');

        $this->client->request(
            method: Request::METHOD_POST,
            uri: '/api/v2/admin/administrators/reset-password',
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
            uri: '/api/v2/admin/administrators/reset-password/token',
            server: self::PATCH_CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }
}
