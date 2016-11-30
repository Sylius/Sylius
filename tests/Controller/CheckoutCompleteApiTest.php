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

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutCompleteApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_order_checkout_complete_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/checkouts/complete/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/checkouts/complete/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_order_that_is_not_addressed_and_has_no_shipping_and_payment_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());

        $url = sprintf('/api/checkouts/complete/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/complete_invalid_order_state', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_order_that_is_addressed_and_has_shipping_and_payment_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());
        $this->selectOrderPaymentMethod($orderId, $checkoutData['cash_on_delivery']->getId());

        $url = sprintf('/api/checkouts/complete/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', sprintf('/api/checkouts/%d', $checkoutData['order1']->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/completed_order_response');
    }
}
