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
use Symfony\Component\HttpFoundation\Response;

final class CustomersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_registers_customers(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/customers',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'shop@example.com',
                'password' => 'sylius',
                'subscribedToNewsletter' => true,
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_allows_shop_user_to_log_in(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/authentication-token',
            server: self::CONTENT_TYPE_HEADER,
            content: json_encode([
                'email' => 'oliver@doe.com',
                'password' => 'sylius',
            ], \JSON_THROW_ON_ERROR),
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/log_in_customer_response', Response::HTTP_OK);
    }
}
