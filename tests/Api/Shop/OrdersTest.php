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
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\ContentType;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrdersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    private MessageBusInterface $commandBus;

    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();
        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_an_order_as_a_guest_with_a_customer_that_is_already_registered(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);
        $this->updateCartWithAddress($tokenValue);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_items(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);


        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_items_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_adjustments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        /** @var OrderInterface $order */
        $order = $this->get('sylius.repository.order')->findCartByTokenValue($tokenValue);
        $orderItem = $order->getItems()->first();

        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->get('sylius.factory.adjustment')->createNew();

        $adjustment->setType(AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT);
        $adjustment->setAmount(200);
        $adjustment->setNeutral(false);
        $adjustment->setLabel('Test Promotion Adjustment');

        $orderItem->addAdjustment($adjustment);
        $this->get('sylius.manager.order')->flush();

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_item_adjustments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_allows_to_add_items_to_order(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $this->pickUpCart();

        $this->client->request('POST', '/api/v2/shop/orders/nAWw2jewpA/items', [], [], self::CONTENT_TYPE_HEADER, json_encode([
            'productVariant' => '/api/v2/shop/product-variants/MUG_BLUE',
            'quantity' => 3,
        ]));
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/add_item_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_does_not_get_orders_collection_for_guest(): void
    {
        $this->client->request('GET', '/api/v2/shop/orders', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/error/jwt_token_not_found', Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_allows_to_patch_orders_payment_method(): void
    {
        $this->loadFixturesFromFiles(['authentication/customer.yaml', 'channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $loginData = $this->logInShopUser('oliver@doe.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $tokenValue = 'nAWw2jewpA';

        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], $header);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            'PATCH',
            sprintf('/api/v2/shop/account/orders/nAWw2jewpA/payments/%s',$orderResponse['payments'][0]['id']),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
                'HTTP_Authorization' => sprintf('Bearer %s', $loginData)
            ],
                json_encode([
                    'paymentMethod' => '/api/v2/shop/payment-methods/CASH_ON_DELIVERY',
            ])
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/updated_payment_method_on_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_creates_empty_cart_with_provided_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json', 'HTTP_ACCEPT_LANGUAGE' => 'pl_PL'],
            json_encode([])
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/create_cart_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_creates_empty_cart_with_default_locale(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $this->client->request(
            'POST',
            '/api/v2/shop/orders',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'],
            json_encode([])
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/create_cart_with_default_locale_response', Response::HTTP_CREATED);
    }

    /** @test */
    public function it_allows_to_patch_orders_address(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        /** @var CountryInterface $country */
        $country = $fixtures['country_US'];

        $billingAddress = [
            'firstName'=> 'Jane',
            'lastName'=> 'Doe',
            'phoneNumber'=> '666111333',
            'company'=> 'Potato Corp.',
            'countryCode'=> $country->getCode(),
            'street'=> 'Top secret',
            'city'=> 'Nebraska',
            'postcode'=> '12343'
        ];

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/shop/orders/nAWw2jewpA',
            server: [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            content: json_encode([
                'email' => 'oliver@doe.com',
                'billingAddress' => $billingAddress,
            ])
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/updated_billing_address_on_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_removes_item_from_the_cart(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request('GET', sprintf('/api/v2/shop/orders/%s', $tokenValue));
        $itemId = json_decode($this->client->getResponse()->getContent(), true)['items'][0]['id'];

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $itemId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_does_not_remove_item_from_the_cart_if_invalid_uri_item_parameter_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->addItemToCart('MUG_BLUE', 3, $tokenValue);

        $this->client->request('GET', sprintf('/api/v2/shop/orders/%s', $tokenValue));

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/STRING-INSTEAD-OF-ID', $tokenValue));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_returns_unprocessable_entity_status_if_tries_to_remove_an_item_that_not_exist_in_the_order(): void
    {
        $this->loadFixturesFromFile('channel.yaml');

        $tokenValue = $this->pickUpCart();
        $nonExistingOrderItemId = 123;

        $this->client->request('DELETE', sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, $nonExistingOrderItemId));

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_returns_unprocessable_entity_status_if_trying_to_assign_shipping_method_to_non_existing_shipment(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'shipping_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/shipments/%s', $tokenValue, '1237'),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode(['shippingMethod' => 'api/v2/shop/shipping-methods/UPS'])
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/assign_shipping_method_to_non_existing_shipment_response',
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /** @test */
    public function it_returns_unprocessable_entity_status_if_trying_to_change_item_quantity_if_invalid_item_id_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/shop/orders/%s/items/%s', $tokenValue, 'invalid-item-id'),
            server: ContentType::APPLICATION_JSON_MERGE_PATCH,
            content: json_encode(['quantity' => 5])
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_does_not_return_payment_configuration_if_invalid_payment_id_passed(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml']);

        $tokenValue = $this->pickUpCart();

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/orders/%s/payments/%s/configuration', $tokenValue, 'invalid-payment-id'),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->commandBus = self::getContainer()->get('sylius.command_bus');
    }

    private function pickUpCart(): string
    {
        $tokenValue = 'nAWw2jewpA';

        $pickupCartCommand = new PickupCart($tokenValue);
        $pickupCartCommand->setChannelCode('WEB');

        $this->commandBus->dispatch($pickupCartCommand);

        return $tokenValue;
    }

    private function addItemToCart(string $productVariantCode, int $quantity, string $tokenValue): void
    {
        $addItemToCartCommand = new AddItemToCart($productVariantCode, $quantity);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($addItemToCartCommand);
    }

    private function updateCartWithAddress(string $tokenValue): void
    {
        $address = new Address();
        $address->setFirstName('John');
        $address->setLastName('Doe');
        $address->setCity('New York');
        $address->setStreet('Avenue');
        $address->setCountryCode('US');
        $address->setPostcode('90000');

        $updateCartCommand = new UpdateCart(email: 'sylius@example.com', billingAddress: $address);
        $updateCartCommand->setOrderTokenValue($tokenValue);

        $this->commandBus->dispatch($updateCartCommand);
    }
}
