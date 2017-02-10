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
final class ProductVariantApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_product_variant_list_when_access_is_denied()
    {
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants', $product_variants_data['product1']->getId()));
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_variant_when_it_does_not_exist()
    {
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/-1', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product1']->getId(), $product_variants_data['productVariant2']->getCode()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_indexing_product_variants()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_paginating_the_index_of_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), ['page' => 2], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/paginated_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_product_variants()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), ['sorting' => ['position' => 'desc']], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();
        
        $this->assertResponse($response, 'product_variant/sorted_index_response');
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG"
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_variant_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, []);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "translations": {
                "de_CH": {
                    "name": "Monsterbecher"
                },
                "en_US": {
                    "name": "Monster Mug"
                }
            }
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_translations_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_channel_pricings()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "channelPricings": [
                {
                    "price": "1243"
                },
                {
                    "price": "342"
                }
            ]
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_channel_pricings_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_tracked_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "tracked": true,
            "onHand": 5
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_tracked_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "tax_category": "TC1"
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_tax_category_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "shipping_category": "SC1"
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_shipping_category_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_product_option()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "option_values": { 
                "MUG__TYPE": "MUG_TYPE_MEDIUM" 
            }
        }
EOT;
        $this->client->request('POST', sprintf('/api/v1/products/%s/variants/', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_product_option_response', Response::HTTP_CREATED);
    }
    
    /**
     * @test
     */
    public function it_does_not_allow_delete_product_variant_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('DELETE', sprintf('/api/v1/products/%s/variants/-1', $product_variants_data['product1']->getId()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_delete_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $this->client->request('DELETE', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product1']->getId(), $product_variants_data['productVariant1']->getCode()), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product1']->getId(), $product_variants_data['productVariant1']->getCode()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_updating_information_about_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');

        $data =
<<<EOT
        {
            "code": "NEW_MUG_CODE"
        }
EOT;
        $this->client->request('PUT', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product1']->getId(), $product_variants_data['productVariant1']->getCode()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }


    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "translations": {
                "de_CH": {
                    "name": "Monsterbecher"

                }
            }
        }
EOT;
        $this->client->request('PATCH', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product1']->getId(), $product_variants_data['productVariant1']->getCode()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_not_change_on_hand_after_updating_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $product_variants_data = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "tracked": false
        }
EOT;
        $this->client->request('PATCH', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product2']->getId(), $product_variants_data['productVariant21']->getCode()), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', sprintf('/api/v1/products/%s/variants/%s', $product_variants_data['product2']->getId(), $product_variants_data['productVariant21']->getCode()), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/not_changed_on_hand_response', Response::HTTP_OK);
    }
}
