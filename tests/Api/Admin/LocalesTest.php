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
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class LocalesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_locales_as_logged_in_administrator(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/locales',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER)
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'admin/get_locales_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_new_locales(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/locales',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER),
            json_encode([
                'code' => 'ga_IE',
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse($this->client->getResponse(), 'admin/post_locale_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_does_not_allow_creating_a_locale_with_invalid_code(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/locales',
            [],
            [],
            array_merge($header, self::CONTENT_TYPE_HEADER),
            json_encode([
                'code' => 'lol',
            ], JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_locale_with_invalid_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /** @test */
    public function it_denies_access_to_a_locales_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/api_administrator.yaml']);

        $this->client->request('GET', '/api/v2/admin/locales');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
