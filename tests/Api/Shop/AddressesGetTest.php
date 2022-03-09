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

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class AddressesGetTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_get_address_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml']);

        $this->client->request('GET', '/api/v2/shop/addresses', [], [], [self::CONTENT_TYPE_HEADER]);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_address_list_of_an_authorized_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $this->client->request(
            'GET',
            '/api/v2/shop/addresses',
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER)
        );

        $this->assertResponse($this->client->getResponse(), 'shop/get_addresses_response');
    }

    /** @test */
    public function it_returns_an_address_of_the_authorized_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];
        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $authorizationHeader = $this->getAuthorizationHeaderAsCustomer($customer->getEmailCanonical(), 'sylius');

        $this->client->request(
            'GET',
            '/api/v2/shop/addresses/' . $address->getId(),
            [],
            [],
            array_merge($authorizationHeader, self::CONTENT_TYPE_HEADER)
        );

        $this->assertResponse($this->client->getResponse(), 'shop/get_an_address_response');
    }
}
