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
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class PaymentMethodsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_available_payment_methods_from_payments(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/payment-methods?paymentId=%s&tokenValue=%s', $orderResponse['payments'][0]['id'], $tokenValue),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_for_cart_and_payment_response');
    }

    /** @test */
    public function it_gets_empty_response_if_only_payment_id_is_set_in_filter(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA', [], [], self::CONTENT_TYPE_HEADER);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/payment-methods?paymentId=%s', $orderResponse['payments'][0]['id']),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_for_uncompleted_filters_response');
    }

    /** @test */
    public function it_gets_empty_response_if_only_cart_token_is_set_in_filter(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = $this->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart($tokenValue, 'en_US');
        $pickupCartCommand->setChannelCode('WEB');
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3);
        $addItemToCartCommand->setOrderTokenValue($tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $this->client->request(
            'GET',
            sprintf('/api/v2/shop/payment-methods?tokenValue=%s', $tokenValue),
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_for_uncompleted_filters_response');
    }

    /** @test */
    public function it_gets_all_enabled_payment_methods_when_filters_are_not_set(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'payment_method.yaml']);

        $this->client->request(
            'GET',
            '/api/v2/shop/payment-methods',
            [],
            [],
            self::CONTENT_TYPE_HEADER
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_response');
    }
}
