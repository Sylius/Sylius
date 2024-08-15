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

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ShippingMethodsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_all_available_shipping_methods_by_default_in_given_channel(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml']);

        $this->requestGet('/api/v2/shop/shipping-methods');

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_shipping_methods_response');
    }

    /** @test */
    public function it_gets_a_shipping_method(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml']);

        $this->requestGet('/api/v2/shop/shipping-methods/UPS');

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_shipping_method_response');
    }

    /** @test */
    public function it_does_not_get_a_shipping_method_not_available_in_given_channel(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml']);

        $this->requestGet('/api/v2/shop/shipping-methods/FEDEX');

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_shipping_methods_available_for_given_shipment_and_order(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml']);

        $tokenValue = 'nAWw2jewpA';
        $this->getCartAndPutItemForCustomer($tokenValue, 'sylius@example.com');

        $this->requestGet(sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->requestGet(sprintf(
            '/api/v2/shop/orders/%s/shipments/%s/methods',
            $tokenValue,
            $orderResponse['shipments'][0]['id'],
        ));

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_order_shipping_methods_response');
    }

    /** @test */
    public function it_gets_available_shipping_methods_of_assigned_cart_for_visitor(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->getCartAndPutItemForCustomer($tokenValue, 'oliver@doe.com');

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->get('sylius.repository.shipment');
        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentRepository->findOneBy([]);

        $this->requestGet(sprintf('/api/v2/shop/orders/%s/shipments/%s/methods', $tokenValue, $shipment->getId()));

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_order_shipping_methods_response');
    }

    /** @test */
    public function it_gets_available_shipping_methods_of_assigned_cart_for_other_users_if_shipment_id_and_cart_provided(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->getCartAndPutItemForCustomer($tokenValue, 'oliver@doe.com');

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->get('sylius.repository.shipment');

        /** @var ShipmentInterface $shipment */
        $shipment = $shipmentRepository->findOneBy([]);

        $this->requestGet(
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s/methods', $tokenValue, $shipment->getId()),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_order_shipping_methods_response');
    }

    /** @test */
    public function it_gets_empty_list_of_available_shipping_methods_for_not_existent_shipment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $this->requestGet('/api/v2/shop/orders/nAWw2jewpA/shipments/-10/methods');

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_empty_order_shipping_methods_response');
    }

    /** @test */
    public function it_gets_empty_list_of_available_shipping_methods_for_not_existent_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
        ]);

        $this->requestGet('/api/v2/shop/orders/test/shipments/-10/methods');

        $this->assertResponse($this->client->getResponse(), 'shop/shipping_method/get_empty_order_shipping_methods_response');
    }

    private function getCartAndPutItemForCustomer(string $tokenValue, string $customerEmail): void
    {
        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart(tokenValue: $tokenValue, channelCode: 'WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3, $tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart(email: $customerEmail, billingAddress: $address, orderTokenValue: $tokenValue);
        $commandBus->dispatch($updateCartCommand);
    }
}
