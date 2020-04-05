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

namespace Sylius\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

final class CheckoutAddressingApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_order_addressing_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/checkouts/addressing/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_address_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/checkouts/addressing/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_address_order_without_specifying_shipping_address()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/addressing_validation_failed_shipping_address', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_address_order_with_the_same_shipping_and_billing_address()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     *
     * It is a test for deprecated behaviour since Sylius 1.7 and will be removed in Sylius 2.0
     */
    public function it_allows_to_address_order_with_the_same_shipping_and_billing_address_using_different_billing_address_flag(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "shippingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentBillingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_address_order_with_different_addresses_if_billing_address_is_not_defined()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/customers.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentShippingAddress": true
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/addressing_validation_failed_billing_address', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_address_order_with_different_shipping_and_billing_address()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "shippingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentShippingAddress": true
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCheckoutSummaryUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/addressed_order_response');
    }

    /**
     * @test
     *
     * It is a test for deprecated behaviour since Sylius 1.7 and will be removed in Sylius 2.0
     */
    public function it_allows_to_address_order_with_different_shipping_and_billing_address_using_different_billing_address_flag(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "shippingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentBillingAddress": true
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCheckoutSummaryUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/addressed_order_response');
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_address_after_the_order_has_already_been_addressed()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $data =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $newData =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $newData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_address_after_selecting_shipping_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $addressData =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $addressData);

        $this->selectOrderShippingMethod($cartId);

        $newAddressData =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $newAddressData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_to_change_order_address_after_selecting_payment_method()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/countries.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);

        $addressData =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $addressData);

        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        $newAddressData =
<<<EOT
        {
            "billingAddress": {
                "firstName": "Hieronim",
                "lastName": "Bosch",
                "street": "Surrealism St.",
                "countryCode": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "differentShippingAddress": false
        }
EOT;

        $this->client->request('PUT', $this->getAddressingUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $newAddressData);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @return string
     */
    private function getAddressingUrl($cartId)
    {
        return sprintf('/api/v1/checkouts/addressing/%d', $cartId);
    }
}
