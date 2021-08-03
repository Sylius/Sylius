<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Api\Shop;

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class CustomerUpdateTest extends JsonApiTestCase
{
    /** @test */
    public function it_returns_small_amount_of_data_on_customer_update(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml']);
        $loginData = $this->loginAsCustomer();

        $this->client->request(
            'PUT',
            $loginData['customer'],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
                'HTTP_Authorization' => sprintf('Bearer %s', $loginData['token'])
            ],
            json_encode([
                'firstName' => 'John'
            ])
        );

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/update_customer_response', Response::HTTP_OK);
    }

    private function loginAsCustomer(): array
    {
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

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
