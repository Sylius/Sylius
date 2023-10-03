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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class AddressesGetTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_denies_access_to_get_address_list_for_not_authenticated_user(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml']);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses', server: self::CONTENT_TYPE_HEADER);

        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function it_returns_address_list_of_an_authorized_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses', server: $header);

        $this->assertResponse($this->client->getResponse(), 'shop/address/get_addresses_response');
    }

    /** @test */
    public function it_returns_an_address_of_the_authorized_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_tony'];
        /** @var AddressInterface $address */
        $address = $fixtures['address'];

        $header = array_merge($this->logInShopUser($customer->getEmailCanonical()), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/addresses/' . $address->getId(), server: $header);

        $this->assertResponse($this->client->getResponse(), 'shop/address/get_an_address_response');
    }
}
