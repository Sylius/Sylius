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
use Sylius\Bundle\AdminApiBundle\Form\Type\ProductVariantType;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
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
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantListUrl($product));
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_product_variant_when_it_does_not_exist()
    {
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantListUrl($product) . 'code', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantsData['productVariant2'];

        $this->client->request('GET', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_indexing_product_variants()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_paginating_the_index_of_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantListUrl($product), ['page' => 2], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/paginated_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_product_variants()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantListUrl($product), ['sorting' => ['position' => 'desc']], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/sorted_index_response');
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG"
        }
EOT;

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_product_variant_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, []);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

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

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_translations_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_channel_pricings()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];


        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "channelPricings": {
                "WEB": {
                    "price": "1243"
                },
                "MOB": {
                    "price": "342"
                }
            }
        }
EOT;
        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_channel_pricings_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_tracked_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "tracked": true,
            "onHand": 5
        }
EOT;

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_tracked_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "taxCategory": "TC1"
        }
EOT;

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_tax_category_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_create_product_variant_with_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "shippingCategory": "SC1"
        }
EOT;

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
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
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $data =
<<<EOT
        {
            "code": "MONSTER_MUG",
            "optionValues": { 
                "MUG_TYPE": "MUG_TYPE_MEDIUM" 
            }
        }
EOT;

        $this->client->request('POST', $this->getVariantListUrl($product), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/create_with_product_option_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_delete_product_variant_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('DELETE', $this->getVariantListUrl($product) . 'code', [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_delete_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantsData['productVariant1'];

        $this->client->request('DELETE', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        $this->client->request('GET', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_updating_information_about_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantsData['productVariant1'];

        $version = $productVariantsData['productVariant1']->getVersion();

        $data =
<<<EOT
        {
            "code": "NEW_MUG_CODE",
            "version": $version
        }
EOT;
        $this->client->request('PUT', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }


    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product1'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantsData['productVariant1'];

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

        $this->client->request('PATCH', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_not_change_on_hand_after_updating_product_variant()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $productVariantsData = $this->loadFixturesFromFile('resources/product_variants.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        /** @var ProductInterface $product */
        $product = $productVariantsData['product2'];

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $productVariantsData['productVariant21'];

        $data =
<<<EOT
        {
            "tracked": false
        }
EOT;
        $this->client->request('PATCH', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getVariantUrl($product, $productVariant), [], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_variant/not_changed_on_hand_response', Response::HTTP_OK);
    }

    /**
     * @param ProductInterface $product
     *
     * @return string
     */
    private function getVariantListUrl(ProductInterface $product)
    {
        return sprintf('/api/v1/products/%s/variants/', $product->getCode());
    }

    /**
     * @param ProductInterface $product
     * @param ProductVariantInterface $productVariant
     *
     * @return string
     */
    private function getVariantUrl(ProductInterface $product, ProductVariantInterface $productVariant)
    {
        return sprintf('%s%s', $this->getVariantListUrl($product), $productVariant->getCode());
    }
}
