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

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class LocalesTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_does_not_allow_creating_a_locale_with_invalid_code(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            'POST',
            '/api/v2/admin/locales',
            [],
            [],
            $header,
            json_encode([
                'code' => 'lol',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/post_locale_with_invalid_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_denies_access_to_a_locales_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/locales');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_a_locale(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var LocaleInterface $locale */
        $locale = $fixtures['locale_ga'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/locales/%s', $locale->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/locale/get_locale_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_locales(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/locales', server: $header);

        $this->assertResponse($this->client->getResponse(), 'admin/locale/get_locales_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_a_locale(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/locales',
            server: $header,
            content: json_encode([
                'code' => 'is_IS',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/locale/post_locale_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_deletes_an_unused_locale(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'DELETE',
            uri: '/api/v2/admin/locales/en_US',
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/locales/en_US',
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
