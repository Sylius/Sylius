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
final class CheckoutPaymentApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_order_payment_selection_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/checkouts/select-payment/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_payment_for_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/checkouts/select-payment/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_payment_for_order_that_is_not_addressed_and_has_no_shipping_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $orders = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $orders['order1']->getId();
        $this->addressOrder($orderId);

        $url = sprintf('/api/checkouts/select-payment/%d', $orderId);
        $this->client->request('PATCH', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/payment_invalid_order_state', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_unexisting_payment_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());

        $data =
<<<EOT
        {
            "payments": [
                {
                    "method": 0
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/payment_validation_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_select_payment_method_for_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());

        $data =
<<<EOT
        {
            "payments": [
                {
                    "method": {$checkoutData['cash_on_delivery']->getId()}
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', sprintf('/api/checkouts/%d', $checkoutData['order1']->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/payment_selected_order_response');
    }

    /**
     * @test
     */
    public function it_allows_to_change_payment_method_after_its_already_been_chosen()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());
        $this->selectOrderPaymentMethod($orderId, $checkoutData['cash_on_delivery']->getId());

        $data =
<<<EOT
        {
            "payments": [
                {
                    "method": {$checkoutData['pay_by_check']->getId()}
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }
}
