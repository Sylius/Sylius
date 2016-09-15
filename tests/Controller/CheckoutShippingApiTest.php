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
final class CheckoutShippingApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_order_shipping_selection_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/checkouts/select-shipping/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/checkouts/select-shipping/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_that_is_not_addressed()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $url = sprintf('/api/checkouts/select-shipping/%d', $checkoutData['order1']->getId());
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_invalid_order_state', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_without_specifying_shipping_method()
    {
        // TO-DO
        // feature cannot be tested properly due to bug with cascade shipments validation
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_unexisting_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": 0
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'checkout/shipping_validation_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_select_shipping_method_for_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$checkoutData['ups']->getCode()}"
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', sprintf('/api/checkouts/%d', $checkoutData['order1']->getId()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_selected_order_response');
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_shipping_method_after_its_already_been_chosen()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $checkoutData = $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $checkoutData['order1']->getId();
        $this->addressOrder($orderId);
        $this->selectOrderShippingMethod($orderId, $checkoutData['ups']->getCode());

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$checkoutData['dhl']->getCode()}"
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_shipping_method_after_selecting_payment_method()
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
            "shipments": [
                {
                    "method": "{$checkoutData['dhl']->getCode()}"
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }
}
