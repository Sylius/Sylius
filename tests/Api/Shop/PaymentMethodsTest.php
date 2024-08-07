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
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class PaymentMethodsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_payment_methods_available_for_given_payment_and_order(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $tokenValue = 'nAWw2jewpA';

        /** @var MessageBusInterface $commandBus */
        $commandBus = self::getContainer()->get('sylius.command_bus');

        $pickupCartCommand = new PickupCart(
            tokenValue: $tokenValue,
            channelCode: 'WEB',
            localeCode: 'en_US',
        );
        $commandBus->dispatch($pickupCartCommand);

        $addItemToCartCommand = new AddItemToCart('MUG_BLUE', 3, $tokenValue);
        $commandBus->dispatch($addItemToCartCommand);

        $this->client->request(method: 'GET', uri: '/api/v2/shop/orders/nAWw2jewpA', server: self::CONTENT_TYPE_HEADER);
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/orders/%s/payments/%s/methods', $tokenValue, $orderResponse['payments'][0]['id']),
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_for_cart_and_payment_response');
    }

    /** @test */
    public function it_gets_all_enabled_payment_methods(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'payment_method.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/payment-methods',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_methods_response');
    }

    /** @test */
    public function it_gets_payment_method_by_code(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'payment_method.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/payment-methods/PAYPAL',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/payment_method/get_payment_method_response');
    }

    /** @test */
    public function it_gets_nothing_if_desired_payment_method_is_disabled(): void
    {
        $this->loadFixturesFromFiles(['channel.yaml', 'cart.yaml', 'payment_method.yaml']);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/payment-methods/DISABLED_PAYMENT_METHOD',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
