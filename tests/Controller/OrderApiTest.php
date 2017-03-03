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
        $this->client->request('GET', $this->getOrderUrl(-1));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', $this->getOrderUrl(-1), [], [], static::$authorizedHeaderWithAccept);

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

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_an_order_with_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');
        $this->loadFixturesFromFile('resources/checkout_promotion.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_with_promotion_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_an_order_with_coupon_based_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');
        $this->loadFixturesFromFile('resources/checkout_coupon_based_promotion.yml');
        $cartId = $this->createCart();

        $this->addItemToCart($cartId);

        $this->client->request('PATCH',  '/api/v1/carts/' . $cartId, [], [], static::$authorizedHeaderWithAccept, '{"promotionCoupon": "BANANAS"}');

        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);
        $this->completeOrder($cartId);

        $this->client->request('GET', $this->getOrderUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_with_coupon_based_promotion_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_canceling_an_order_for_non_authenticated_user()
    {
        $this->client->request('PUT', $this->getCancelUrl(-1));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_canceling_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', $this->getCancelUrl(-1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_cancel_an_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('PUT', $this->getCancelUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_canceled_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_shipping_an_order_for_non_authenticated_user()
    {
        $this->client->request('PUT', $this->getShipOrderShipmentUrl(-1, -1));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_ship_an_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', $this->getShipOrderShipmentUrl(-1, -1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_shipping_does_not_exist_for_the_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('PUT', $this->getShipOrderShipmentUrl($orderId, -1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_ship_an_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $this->client->request('PUT', $this->getShipOrderShipmentUrl($orderId, $rawResponse['shipments'][0]['id']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_shipped_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_ship_an_order_with_shipment_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);


        $data =
<<<EOT
        {
            "tracking": "BANANAS"
        }
EOT;

        $this->client->request('PUT', $this->getShipOrderShipmentUrl($orderId, $rawResponse['shipments'][0]['id']), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_shipped_with_tracking_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_completing_the_payment_for_the_order_for_non_authenticated_user()
    {
        $this->client->request('PUT', $this->getCompleteOrderPaymentUrl(-1, -1));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_completing_the_payment_for_the_order_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', $this->getShipOrderShipmentUrl(-1, -1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_completing_payment_does_not_exist_for_the_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('PUT', $this->getCompleteOrderPaymentUrl($orderId, -1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_the_payment_for_the_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $this->client->request('PUT', $this->getCompleteOrderPaymentUrl($orderId, $rawResponse['payments'][0]['id']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_payed_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_the_payment_and_ship_the_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $this->client->request('PUT', $this->getShipOrderShipmentUrl($orderId, $rawResponse['shipments'][0]['id']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('PUT', $this->getCompleteOrderPaymentUrl($orderId, $rawResponse['payments'][0]['id']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'order/order_fulfilled_show_response', Response::HTTP_OK);
    }

    /**
     * @param mixed $orderId
     *
     * @return string
     */
    private function getOrderUrl($orderId)
    {
        return '/api/v1/orders/' . $orderId;
    }

    /**
     * @param mixed $orderId
     * @param mixed $shipmentId
     *
     * @return string
     */
    private function getShipOrderShipmentUrl($orderId, $shipmentId)
    {
        return sprintf('%s/shipments/%s/ship', $this->getOrderUrl($orderId), $shipmentId);
    }

    /**
     * @param mixed $orderId
     * @param mixed $paymentId
     *
     * @return string
     */
    private function getCompleteOrderPaymentUrl($orderId, $paymentId)
    {
        return sprintf('%s/payments/%s/complete', $this->getOrderUrl($orderId), $paymentId);
    }

    /**
     * @param mixed $orderId
     *
     * @return string
     */
    private function getCancelUrl($orderId)
    {
        return $this->getOrderUrl($orderId) . '/cancel';
    }
}
