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
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CheckoutApiTestCase extends JsonApiTestCase
{
    /**
     * @var array
     */
    protected static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @var array
     */
    protected static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @return mixed
     */
    protected function createCart()
    {
        $data =
<<<EOT
        {
            "customer": "oliver.queen@star-city.com",
            "channel": "CHANNEL",
            "locale_code": "en_US"
        }
EOT;

        $this->client->request('POST', '/api/v1/carts/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        return $rawResponse['id'];
    }

    /**
     * @param mixed $cartId
     */
    protected function addItemToCart($cartId)
    {
        $url = sprintf('/api/v1/carts/%d/items/', $cartId);

        $data =
<<<EOT
        {
            "variant": "MUG_SW",
            "quantity": 1
        }
EOT;

        $this->client->request('POST', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param mixed $cartId
     */
    protected function addressOrder($cartId)
    {
        $this->loadFixturesFromFile('resources/countries.yml');

        $data =
<<<EOT
        {
            "shipping_address": {
                "first_name": "Hieronim",
                "last_name": "Bosch",
                "street": "Surrealism St.",
                "country_code": "NL",
                "city": "’s-Hertogenbosch",
                "postcode": "99-999"
            },
            "billing_address": {
                "first_name": "Vincent",
                "last_name": "van Gogh",
                "street": "Post-Impressionism St.",
                "country_code": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "different_billing_address": true
        }
EOT;

        $url = sprintf('/api/v1/checkouts/addressing/%d', $cartId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param mixed $cartId
     */
    protected function selectOrderShippingMethod($cartId)
    {
        $url = sprintf('/api/v1/checkouts/select-shipping/%d', $cartId);

        $this->client->request('GET', $url, [], [], static::$authorizedHeaderWithContentType);

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

        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param mixed $cartId
     */
    protected function selectOrderPaymentMethod($cartId)
    {
        $url = sprintf('/api/v1/checkouts/select-payment/%d', $cartId);

        $this->client->request('GET', $url, [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $data =
<<<EOT
        {
            "payments": [
                {
                    "method": "{$rawResponse['payments'][0]['methods'][0]['code']}"
                }
            ]
        }
EOT;

        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param mixed $cartId
     *
     * @return string
     */
    protected function getCheckoutSummaryUrl($cartId)
    {
        return sprintf('/api/v1/checkouts/%d', $cartId);
    }
}
