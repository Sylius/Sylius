<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class CustomersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_returns_small_amount_of_data_on_customer_update(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/customer.yaml']);
        $token = $this->logInShopUser('oliver@doe.com');

        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $this->client->request(
            'PUT',
            '/api/v2/shop/customers/' . $customer->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token)
            ],
            json_encode([
                'firstName' => 'John'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/update_customer_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_registers_customers(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/customers',
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            json_encode([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'shop@example.com',
                'password' => 'sylius',
                'subscribedToNewsletter' => true,
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_allows_customer_to_log_in(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/authentication-token',
            [],
            [],
            self::CONTENT_TYPE_HEADER,
            json_encode([
                'email' => 'oliver@doe.com',
                'password' => 'sylius'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/log_in_customer_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_customer_to_update_its_data(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/customer.yaml']);
        $token = $this->logInShopUser('oliver@doe.com');

        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $this->client->request(
            'PUT',
            '/api/v2/shop/customers/' . $customer->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
                'HTTP_Authorization' => sprintf('Bearer %s', $token)
            ],
            json_encode([
                'email' => 'john.wick@tarasov.mob',
                'firstName' => 'John',
                'lastName' => 'Wick',
                'gender' => 'm',
                'subscribedToNewsletter' => true
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/updated_gender_customer_response', Response::HTTP_OK);
    }
}
