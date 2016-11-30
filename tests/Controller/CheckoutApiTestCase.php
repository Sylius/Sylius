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
     * @param int $orderId
     */
    protected function addressOrder($orderId)
    {
        $this->loadFixturesFromFile('resources/countries.yml');

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
            "billingAddress": {
                "firstName": "Vincent",
                "lastName": "van Gogh",
                "street": "Post-Impressionism St.",
                "countryCode": "NL",
                "city": "Groot Zundert",
                "postcode": "88-888"
            },
            "differentBillingAddress": true,
            "customer": {
                "email": "john@doe.com"
            }
        }
EOT;

        $url = sprintf('/api/checkouts/addressing/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param int $orderId
     * @param int $shippingMethodCode
     */
    protected function selectOrderShippingMethod($orderId, $shippingMethodCode)
    {
        $data =
<<<EOT
        {
            "shipments": [
                {
                    "method": "{$shippingMethodCode}"
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-shipping/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }

    /**
     * @param int $orderId
     * @param int $paymentMethodId
     */
    protected function selectOrderPaymentMethod($orderId, $paymentMethodId)
    {
        $data =
<<<EOT
        {
            "payments": [
                {
                    "method": {$paymentMethodId}
                }
            ]
        }
EOT;

        $url = sprintf('/api/checkouts/select-payment/%d', $orderId);
        $this->client->request('PUT', $url, [], [], static::$authorizedHeaderWithContentType, $data);
    }
}
