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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class OrdersTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = 'nAWw2jewpA';
        $this->placeOrder($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_prevents_visitors_from_getting_the_item_adjustments_of_a_user_order(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    public function it_returns_nothing_if_a_user_tries_to_get_the_order_item_adjustments_of_another_user(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'authentication/customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $loginData = $this->logInShopUser('dave@doe.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue, 'oliver@doe.com');

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], $header);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_UNAUTHORIZED);
    }

    /** @test */
    public function it_gets_order_item_adjustments(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml', 'cart.yaml', 'country.yaml', 'shipping_method.yaml', 'payment_method.yaml']);

        $tokenValue = $this->pickUpCart();

        $order = $this->createOrderWithOrderItemAdjustments($tokenValue);

        $this->client->request('GET', '/api/v2/shop/orders/nAWw2jewpA/items/'.$order->getItems()->first()->getId().'/adjustments', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/get_order_item_adjustments_response', Response::HTTP_OK);
    }

    private function createOrderWithOrderItemAdjustments(string $tokenValue, string $email = 'sylius@example.com'): OrderInterface
    {
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

        $this->updateCartWithAddress($tokenValue, $email);

        $orderItem->addAdjustment($adjustment);
        $this->get('sylius.manager.order')->flush();

        return $order;
    }
}
