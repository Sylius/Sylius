<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_getting_an_order_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/orders/-1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/orders/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $this->client->request('GET', $this->getOrderUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/cart_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_an_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);
        $this->completeOrder($cartId);

        $this->client->request('GET', $this->getOrderUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_show_response', Response::HTTP_OK);
    }

    /**
     * @param $cartId
     *
     * @return string
     */
    private function getOrderUrl($cartId)
    {
        return '/api/v1/orders/' . $cartId;
    }
}
