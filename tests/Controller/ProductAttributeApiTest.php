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
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductAttributeApiTest extends JsonApiTestCase
{
    /**
     * @var array
     */
    private static $authorizedHeaderWithContentType = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'CONTENT_TYPE' => 'application/json',
    ];

    /**
     * @var array
     */
    private static $authorizedHeaderWithAccept = [
        'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        'ACCEPT' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_attributes_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/product_attributes.yml');

        $this->client->request('GET', '/api/v1/product-attributes/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_product_attributes()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/product_attributes.yml');


        $this->client->request('GET', '/api/v1/product-attributes/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_attribute_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/product-attributes/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $this->client->request('GET', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_product_attribute_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/product-attributes/-1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $this->client->request('DELETE', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/product-attributes/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/index_response_after_delete', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_create_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "mug_material",
            "translations": {
                "de_CH": {
                    "name": "Becher Material"
                },
                "en_US": {
                    "name": "Mug material"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/product-attributes/text', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_attribute_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/product-attributes/text', [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_attribute_without_type()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/product-attributes', [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_METHOD_NOT_ALLOWED);
    }

    /**
     * @test
     */
    public function it_allows_to_update_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $data =
<<<EOT
        {
            "position": 2,
            "translations": {
                "de_CH": {
                    "name": "Becher Material"
                },
                "en_US": {
                    "name": "Mug material"
                }
            }
        }
EOT;

        $this->client->request('PUT', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/show_response_after_update', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_partially_update_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $data =
<<<EOT
        {
            "translations": {
                "de_CH": {
                    "name": "Becher Material"
                },
                "en_US": {
                    "name": "Mug material"
                }
            }
        }
EOT;

        $this->client->request('PATCH', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/show_response_after_partial_update', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_change_product_attribute_type()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $productAttributes = $this->loadFixturesFromFile('resources/product_attributes.yml');
        $productAttribute = $productAttributes['productAttribute1'];

        $data =
<<<EOT
        {
            "type": "integer"
        }
EOT;

        $this->client->request('PATCH', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getProductAttributeUrl($productAttribute), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_create_select_product_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "mug_color",
            "configuration": {
                "choices": [
                    "yellow",
                    "green",
                    "black"
                ],
                "multiple": true,
                "min": 1,
                "max": 2
            },
            "translations": {
                "de_CH": {
                    "name": "Becher Farbe"
                },
                "en_US": {
                    "name": "Mug color"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/product-attributes/select', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_attribute/create_select_response', Response::HTTP_CREATED);
    }

    /**
     * @param ProductAttributeInterface $productAttribute
     *
     * @return string
     */
    private function getProductAttributeUrl(ProductAttributeInterface $productAttribute)
    {
        return '/api/v1/product-attributes/' . $productAttribute->getCode();
    }
}
