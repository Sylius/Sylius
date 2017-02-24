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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
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
        $this->client->request('PUT', '/api/v1/checkouts/select-shipping/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/checkouts/select-shipping/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_that_is_not_addressed()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_invalid_order_state', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_shipping_for_order_without_specifying_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_invalid_order_state', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_provide_details_of_unexisting_cart()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/checkouts/select-shipping/0', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_provides_details_about_available_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);

        $this->client->request('GET', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_shipping_methods', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_provide_details_about_available_shipping_method_before_addressing()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $this->client->request('GET', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/get_available_shipping_methods_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_select_unexisting_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);

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

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_validation_failed', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_select_shipping_method_for_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);

        $this->client->request('GET', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$rawResponse['shipments'][0]['methods'][0]['code']}"
                }
            ]
        }
EOT;

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCheckoutSummaryUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/shipping_selected_order_response');
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_shipping_method_after_its_already_been_chosen()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);

        $this->client->request('GET', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$rawResponse['shipments'][0]['methods'][0]['code']}"
                }
            ]
        }
EOT;

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_shipping_method_after_selecting_payment_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        $this->client->request('GET', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$rawResponse['shipments'][0]['methods'][0]['code']}"
                }
            ]
        }
EOT;

        $this->client->request('PUT', $this->getSelectShippingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $cartId
     *
     * @return string
     */
    private function getSelectShippingUrl($cartId)
    {
        return sprintf('/api/v1/checkouts/select-shipping/%d', $cartId);
    }
}
