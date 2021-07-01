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
use Sylius\Bundle\ApiBundle\Command\Checkout\AddressOrder;
use Sylius\Component\Core\Model\Address;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrdersTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_an_order(): void
    {
        $this->loadFixturesFromFiles(['cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

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

        $addressOrderCommand = new AddressOrder('sylius@example.com', $address);
        $addressOrderCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addressOrderCommand);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_does_not_get_orders_collection_for_guest(): void
    {
        $this->client->request('GET', '/api/v2/shop/orders', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/error/jwt_token_not_found', Response::HTTP_UNAUTHORIZED);
    }
}
