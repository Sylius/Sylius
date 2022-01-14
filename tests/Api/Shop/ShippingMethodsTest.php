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

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShippingMethodsTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;

    /** @test */
    public function it_gets_available_order_shipping_methods(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml']);

        $tokenValue = 'nAWw2jewpA';
        $customer = 'sylius@example.com';
        $this->getCartAndPutItemForCustomer($tokenValue, $customer);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/orders/nAWw2jewpA/shipments/%s/methods', $orderResponse['shipments'][0]['id']),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_shipping_methods_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_empty_list_of_available_shipping_methods_of_assigned_cart_for_visitor(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $customer = 'oliver@doe.com';

        $this->getCartAndPutItemForCustomer($tokenValue, $customer);

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->get('sylius.repository.shipment');

        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentRepository->findOneBy([]);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/orders/nAWw2jewpA/shipments/%s/methods', $shipment->getId()),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_empty_order_shipping_methods_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_empty_list_of_available_shipping_methods_of_assigned_cart_for_other_users(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $customer = 'oliver@doe.com';
        $otherCustomer = 'dave@doe.com';

        $this->getCartAndPutItemForCustomer($tokenValue, $customer);

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->get('sylius.repository.shipment');

        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentRepository->findOneBy([]);

        $this->logInShopUser($otherCustomer);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/orders/nAWw2jewpA/shipments/%s/methods', $shipment->getId()),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_empty_order_shipping_methods_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_empty_list_of_available_shipping_methods_for_not_existent_shipment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $customer = 'oliver@doe.com';

        $this->getCartAndPutItemForCustomer($tokenValue, $customer);

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->get('sylius.repository.shipment');

        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentRepository->findOneBy([]);

        $this->client->request(
            'GET',
            '/api/v2/shop/orders/nAWw2jewpA/shipments/-1/methods',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_empty_order_shipping_methods_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_empty_list_of_available_shipping_methods_for_not_existent_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $this->client->request(
            'GET',
            '/api/v2/shop/orders/nAWw2jewpA/shipments/-1/methods',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_empty_shipping_methods_response', Response::HTTP_OK);
    }

    private function getCartAndPutItemForCustomer(string $tokenValue, string $customer): void
    {
        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart($customer, $address);
        $updateCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($updateCartCommand);
    }
}
