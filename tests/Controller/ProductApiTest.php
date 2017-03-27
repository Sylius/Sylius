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
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $product = $products['product1'];

        $this->client->request('GET', $this->getProductUrl($product), [], [], static::$authorizedHeaderWithAccept);

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
        $product = $products['product1'];

        $this->client->request('DELETE', $this->getProductUrl($product), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
        $product = $products['product1'];

        $this->client->request('GET', $this->getProductUrl($product), [], [], static::$authorizedHeaderWithAccept);

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
                "nl_NL": {
                    "name": "Mok van het thema",
                    "slug": "mok-van-het-thema"
                },
                "en_US": {
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
        $product = $products["product3"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                  "name": "Star Wars",
                  "slug": "star-wars"
                }
            }
        }
EOT;
        $this->client->request('PUT', $this->getProductUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
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
        $product = $products["product1"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                    "name": "Mug Star Wars Episode V"
                }
            }
        }
EOT;
        $this->client->request('PATCH', $this->getProductUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
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

    /**
     * @test
     */
    public function it_allows_creating_product_with_options()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/product_options.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "options": [
                 "MUG_SIZE",
                 "MUG_COLOR"
            ],
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_options_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_main_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "mainTaxon": "MUGS",
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_main_taxon_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_product_taxons()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            },
            "productTaxons": "category,mugs"
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_product_taxons_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_channels()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "channels": ["MOB", "WEB"],
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_channels_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_attributes()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/product_attributes.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "attributes": [
                {
                    "attribute": "mug_material",
                    "localeCode": "en_US",
                    "value": "concrete"
                },
                {
                    "attribute": "mug_collection",
                    "localeCode": "en_US",
                    "value": "make life harder"
                }
            ],
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_attributes_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_select_attribute()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/product_attributes.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "attributes": [
                {
                    "attribute": "mug_color",
                    "localeCode": "en_US",
                    "value": [
                        "green", 
                        "yellow"
                    ]
                }
            ],
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_select_attribute_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_images()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "images": [
                {
                    "type": "FORD_MUG"
                },
                {
                    "type": "MUGS"
                }
            ],
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request(
            'POST',
            '/api/v1/products/',
            [],
            ['images' => [
                ['file' => new UploadedFile(sprintf('%s/../Resources/fixtures/ford.jpg', __DIR__), "ford")],
                ['file' => new UploadedFile(sprintf('%s/../Resources/fixtures/mugs.jpg', __DIR__), "mugs")],
            ]],
            static::$authorizedHeaderWithContentType,
            $data
        );

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_images_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_product_with_associations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/associations.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/products.yml');

        $data =
<<<EOT
        {
            "code": "MUG_TH",
            "associations": {
                "similar": "MUG1,MUG_SW",
                "accessories": "MUG_LOTR,MUG_BB"
            },
            "translations": {
                "en_US": {
                    "name": "Theme Mug",
                    "slug": "theme-mug"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/products/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'product/create_with_associations_response', Response::HTTP_CREATED);
    }

    /**
     * @param ProductInterface $product
     *
     * @return string
     */
    private function getProductUrl(ProductInterface $product)
    {
        return '/api/v1/products/' . $product->getCode();
    }
}
