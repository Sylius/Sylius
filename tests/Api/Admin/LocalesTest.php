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

        $this->client->request('GET', '/api/v2/admin/locales');

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_gets_a_locale(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'locale.yaml']);
        $header = $this->getLoggedHeader();

        /** @var LocaleInterface $locale */
        $locale = $fixtures['locale_ga'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/locales/%s', $locale->getCode()),
            [],
            [],
            $header,
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
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/locales',
            [],
            [],
            $header,
        );

        $this->assertResponse($this->client->getResponse(), 'admin/locale/get_locales_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_a_locale(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'POST',
            '/api/v2/admin/locales',
            [],
            [],
            $header,
            json_encode([
                'code' => 'is_IS',
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/locale/post_locale_response',
            Response::HTTP_CREATED,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
