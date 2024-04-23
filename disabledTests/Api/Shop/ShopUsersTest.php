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

namespace Sylius\Tests\Api\Shop;

use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ShopUsersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_sends_shop_user_password_reset_email(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/shop_user.yaml']);

        $this->client->request(
            method: Request::METHOD_POST,
            uri: '/api/v2/shop/reset-password',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'api@example.com',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_resets_shop_user_password_with_valid_token(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/shop_user_with_reset_password_token.yaml']);

        $validToken = 'valid_token';

        $this->client->request(
            method: Request::METHOD_PATCH,
            uri: sprintf('/api/v2/shop/reset-password/%s', $validToken),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_ACCEPTED);
    }


    /** @test */
    public function it_prevents_shop_user_from_resetting_password_with_invalid_token(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/shop_user_with_reset_password_token.yaml']);

        $validToken = 'invalid_token';

        $this->client->request(
            method: Request::METHOD_PATCH,
            uri: sprintf('/api/v2/shop/reset-password/%s', $validToken),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    /** @test */
    public function it_prevents_shop_user_from_resetting_password_with_expired_token(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'authentication/shop_user_with_expired_reset_password_token.yaml']);

        $validToken = 'valid_token';

        $this->client->request(
            method: Request::METHOD_PATCH,
            uri: sprintf('/api/v2/shop/reset-password/%s', $validToken),
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ], JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
