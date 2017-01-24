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
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_products_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/products.yml');
        $this->client->request('GET', '/api/v1/products/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/products/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_indexing_products()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/many_products.yml');


        $this->client->request('GET', '/api/v1/products/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_showing_product()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $products = $this->loadFixturesFromFile('resources/products.yml');

        $this->client->request('GET', '/api/v1/products/'.$products['product1']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_delete_product_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/products/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_delete_product()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $products = $this->loadFixturesFromFile('resources/products.yml');

        $this->client->request('DELETE', '/api/v1/products/'.$products['product1']->getId(), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', '/api/v1/products/'.$products['product1']->getId(), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_create_product_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "translations": {
                "nl": {
                    "name": "Theme Mug",
                    "slug": "theme-mug2"
                },
                "en": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_updating_product()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $products = $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "translations": {
                "en__US": {
                  "name": "Star Wars",
                  "slug": "star-wars"
                }
            }
        }
EOT;
        $this->client->request('PUT', '/api/v1/products/'. $products["product3"]->getId(), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_product()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $products = $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "translations": {
                "en": {
                    "name": "Mug Star Wars Episode V"
                }
            }
        }
EOT;
        $this->client->request('PATCH', '/api/v1/products/'. $products["product1"]->getId(), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_paginating_the_index_of_products()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/many_products.yml');

        $this->client->request('GET', '/api/v1/products/', ['page' => 2], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/paginated_index_response');
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_products()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/many_products.yml');

        $this->client->request('GET', '/api/v1/products/', ['sorting' => ['code' => 'asc']], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/sorted_index_response');
    }
}
