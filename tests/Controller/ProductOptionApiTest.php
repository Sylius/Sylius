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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author alcaeus <alcaeus@alcaeus.org>
 */
final class ProductOptionTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_product_options_list_when_access_is_denied()
    {
        $this->client->request('GET', '/api/v1/product-options/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_listing_product_options()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/product_options.yml');

        $this->client->request('GET', '/api/v1/product-options/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_option/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_delete_product_option_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/product-options/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_delete_product_option()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productOptions = $this->loadFixturesFromFile('resources/product_options.yml');

        $this->client->request('DELETE', '/api/v1/product-options/'.$productOptions['productOption1']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/product-options/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_option/index_response_after_delete', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_create_product_option_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MUG_SIZE",
            "translations": {
                "de_CH": {
                    "name": "Bechergröße"
                },
                "en_US": {
                    "name": "Mug size"
                }
            },
            "values": [
                {
                    "code": "MUG_SIZE_SMALL",
                    "translations": {
                        "de_CH": {
                            "value": "Klein"
                        },
                        "en_US": {
                            "value": "Small"
                        }
                    }
                },
                {
                    "code": "MUG_SIZE_LARGE",
                    "translations": {
                        "de_CH": {
                            "value": "Groß"
                        },
                        "en_US": {
                            "value": "Large"
                        }
                    }
                }
            ]
        }
EOT;

        $this->client->request('POST', '/api/v1/product-options/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_option/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_option_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/product-options/', [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product_option/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }
}
