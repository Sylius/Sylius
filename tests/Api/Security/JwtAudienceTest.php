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

namespace Sylius\Tests\Api\Security;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\UserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * An administrator and a shop customer may legitimately share an e-mail address, as they live in
 * separate tables. A token must therefore only be accepted by the API section it has been issued for.
 */
final class JwtAudienceTest extends JsonApiTestCase
{
    use UserLoginTrait;

    private const SHARED_EMAIL = 'shared@example.com';

    private const FIXTURES = ['authentication/admin_and_shop_user_sharing_an_email.yaml'];

    #[Test]
    public function it_accepts_an_administrator_token_on_the_admin_api(): void
    {
        $this->loadFixturesFromFiles(self::FIXTURES);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders',
            server: array_merge($this->logInUser('admin', self::SHARED_EMAIL), self::CONTENT_TYPE_HEADER),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    #[Test]
    public function it_accepts_a_shop_user_token_on_the_shop_api(): void
    {
        $this->loadFixturesFromFiles(self::FIXTURES);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/addresses',
            server: array_merge($this->logInUser('shop', self::SHARED_EMAIL), self::CONTENT_TYPE_HEADER),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);
    }

    #[Test]
    public function it_denies_a_shop_user_token_used_on_the_admin_api(): void
    {
        $this->loadFixturesFromFiles(self::FIXTURES);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/orders',
            server: array_merge($this->logInUser('shop', self::SHARED_EMAIL), self::CONTENT_TYPE_HEADER),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }

    #[Test]
    public function it_denies_an_administrator_token_used_on_the_shop_api(): void
    {
        $this->loadFixturesFromFiles(self::FIXTURES);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/addresses',
            server: array_merge($this->logInUser('admin', self::SHARED_EMAIL), self::CONTENT_TYPE_HEADER),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNAUTHORIZED);
    }
}
