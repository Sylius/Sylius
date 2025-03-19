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
    public function it_denies_access_to_a_locales_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);

        $this->requestGet('/api/v2/admin/locales');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_gets_a_locale(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);

        /** @var LocaleInterface $locale */
        $locale = $fixtures['locale_ga'];

        $this->requestGet(
            uri: sprintf('/api/v2/admin/locales/%s', $locale->getCode()),
            headers: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'admin/locale/get_locale_response');
    }

    /** @test */
    public function it_gets_locales(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);

        $this->requestGet(
            uri: '/api/v2/admin/locales',
            headers: $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'admin/locale/get_locales_response');
    }

    /** @test */
    public function it_creates_a_locale(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withAdminUserAuthorization('api@example.com')->build();

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
    public function it_does_not_allow_creating_a_locale_with_invalid_code(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'authentication/api_administrator.yaml']);
        $header = $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withAdminUserAuthorization('api@example.com')->build();

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/locales',
            server: $header,
            content: json_encode([
                'code' => 'invalid',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/locale/post_locale_with_invalid_code_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_deletes_an_unused_locale(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);
        $headers = $this->headerBuilder()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build();

        $this->requestDelete(uri: '/api/v2/admin/locales/en_US', headers: $headers);
        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);

        $this->requestGet(uri: '/api/v2/admin/locales/en_US', headers: $headers);
        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }
}
