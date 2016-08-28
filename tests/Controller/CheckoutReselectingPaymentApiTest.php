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
final class CheckoutPaymentShippingApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_reselecting_order_payment_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/checkouts/reselect-payment/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_reselect_paymeny_of_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/checkouts/reselect-payment/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_reselect_payment_of_order_that_has_no_payment_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $url = sprintf('/api/checkouts/reselect-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_reselect_paymeny_of_order_with_paymeny_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getId());
        $this->selectOrderPaymentMethod($orderId, $checkoutData['cash_on_delivery']->getId());

        $url = sprintf('/api/checkouts/reselect-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/reselect_payment_response', Response::HTTP_OK);
    }
}
