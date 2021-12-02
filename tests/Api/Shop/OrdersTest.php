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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrdersTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

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

        $updateCartCommand = new UpdateCart('sylius@example.com', $address);
        $updateCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($updateCartCommand);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_items(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);


        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_items_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_adjustments_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

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

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

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
}
