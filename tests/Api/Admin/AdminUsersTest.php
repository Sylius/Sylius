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
    public function it_allows_admin_user_to_change_timezone(): void
    {
        $fixtures = $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $adminUser = $fixtures['admin'];
        $header = $this->getLoggedHeader();

        $this->client->request(
            'PUT',
            sprintf('/api/v2/admin/administrators/%s', $adminUser->getId()),
            [],
            [],
            $header,
            json_encode([
                'timezone' => 'Europe/Paris'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/administrators/change_timezone_admin_response', Response::HTTP_OK);
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$container->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
