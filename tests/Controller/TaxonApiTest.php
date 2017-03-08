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
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class TaxonApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_taxon_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/taxons.yml');
        $this->client->request('GET', '/api/v1/taxons/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_taxon_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/taxons/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_indexing_taxons()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');

        $this->client->request('GET', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_showing_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons['women'];

        $this->client->request('GET', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_showing_root_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons['category'];

        $this->client->request('GET', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/show_root_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_taxon_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/taxons/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_deleting_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons['men'];

        $this->client->request('DELETE', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_deleting_root_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons['category'];

        $this->client->request('DELETE', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_creating_root_taxon_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');

        $data =
<<<EOT
        {
            "code": "fluffy_pets",
            "translations": {
                "en_US": {
                    "name": "Fluffy Pets",
                    "slug": "fluffy-pets"
                },
                "nl_NL": {
                    "name": "Pluizige Huisdieren",
                    "slug": "pluizige-huisdieren"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_root_with_multiple_translations_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_root_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "fluffy_pets"
        }
EOT;

        $this->client->request('POST', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_root_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_creating_taxon_with_multiple_translations()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');

        $data =
<<<EOT
        {
            "code": "fluffy_pets",
            "parent": "category",
            "translations": {
                "en_US": {
                    "name": "Fluffy Pets",
                    "slug": "fluffy-pets"
                },
                "nl_NL": {
                    "name": "Pluizige Huisdieren",
                    "slug": "pluizige-huisdieren"
                }
            }
        }
EOT;

        $this->client->request('POST', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_with_multiple_translations_response', Response::HTTP_CREATED);
    }


    /**
     * @test
     */
    public function it_allows_creating_taxon_with_parent()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');

        $data =
<<<EOT
        {
            "code": "horror",
            "parent": "books"
        }
EOT;

        $this->client->request('POST', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_with_parent_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_taxon_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/taxons/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_creating_taxon_with_images()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "toys",
            "images": [
                {
                    "type": "ford"                
                },
                {
                    "type": "mugs"
                }
            ]
        }
EOT;

        $this->client->request('POST', '/api/v1/taxons/', [], [
            'images' => [
                ['file' => new UploadedFile(sprintf('%s/../Resources/fixtures/ford.jpg', __DIR__), "ford")],
                ['file' => new UploadedFile(sprintf('%s/../Resources/fixtures/mugs.jpg', __DIR__), "mugs")],
            ]
        ], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/create_with_images_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_updating_taxon_with_parent()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons["women"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                  "name": "Women",
                  "slug": "books/women"
                }
            },
            "parent": "books"
        }
EOT;
        $this->client->request('PUT', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_root_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons["category"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                  "name": "Categories",
                  "slug": "categories"
                }
            }
        }
EOT;
        $this->client->request('PUT', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons["women"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                    "name": "Girl",
                    "slug": "girl"
                }
            }
        }
EOT;
        $this->client->request('PATCH', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_root_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/locales.yml');
        $taxons = $this->loadFixturesFromFile('resources/taxons.yml');
        $taxon = $taxons["category"];

        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                    "name": "Category",
                    "slug": "category"
                }
            }
        }
EOT;
        $this->client->request('PATCH', $this->getTaxonUrl($taxon), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_paginating_the_index_of_taxons()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/taxons.yml');
        $this->loadFixturesFromFile('resources/many_taxons.yml');

        $this->client->request('GET', '/api/v1/taxons/', ['page' => 2], [], static::$authorizedHeaderWithAccept);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/paginated_index_response');
    }

    /**
     * @test
     */
    public function it_allows_to_update_position_of_product_in_a_taxon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $productTaxons = $this->loadFixturesFromFile('resources/product_taxons.yml');

        /** @var TaxonInterface $taxon */
        $taxon = $productTaxons['mugs'];

        $data =
<<<EOT
        {
            "productsPositions": [
                {
                    "productCode": "MUG_SW",
                    "position": 2
                },
                {
                    "productCode": "MUG_BB",
                    "position": 0
                }
            ]
        }
EOT;

        $this->client->request('PUT', $this->getTaxonProductsPositionsChangeUrl($taxon), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_position_of_product_in_a_taxon_with_incorrect_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $productTaxons = $this->loadFixturesFromFile('resources/product_taxons.yml');

        /** @var TaxonInterface $taxon */
        $taxon = $productTaxons['mugs'];

        $data =
<<<EOT
        {
            "productsPositions": [
                {
                    "productCode": "MUG_SW",
                    "position": "second"
                }
            ]
        }
EOT;

        $this->client->request('PUT', $this->getTaxonProductsPositionsChangeUrl($taxon) , [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'taxon/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param TaxonInterface $taxon
     *
     * @return string
     */
    private function getTaxonUrl(TaxonInterface $taxon)
    {
        return '/api/v1/taxons/' . $taxon->getCode();
    }

    /**
     * @param TaxonInterface $taxon
     *
     * @return string
     */
    private function getTaxonProductsPositionsChangeUrl(TaxonInterface $taxon)
    {
        return sprintf('/api/v1/taxons/%s/products', $taxon->getCode());
    }
}
