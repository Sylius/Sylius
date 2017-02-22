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
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TaxCategoryApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_denies_creating_tax_category_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/tax-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_tax_category_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/tax-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "clothing",
            "name": "Clothing",
            "description": "All items classified as clothing."
        }
EOT;

        $this->client->request('POST', '/api/v1/tax-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_tax_categories_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/tax-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_tax_categories()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');

        $this->client->request('GET', '/api/v1/tax-categories/', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_sorted_tax_categories()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories.yml');

        $this->client->request('GET', '/api/v1/tax-categories/', ['sorting' => ['nameAndDescription' => 'asc']], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/sorted_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_tax_categories_list_filtered_by_name()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories_for_filtering.yml');

        $this->client->request('GET', '/api/v1/tax-categories/', ['criteria' => ['search' => 'clothing']], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/filtered_by_name_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_tax_categories_list_filtered_by_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/tax_categories_for_filtering.yml');

        $this->client->request('GET', '/api/v1/tax-categories/', ['criteria' => ['search' => 'TC1']], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/filtered_by_code_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_tax_category_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/tax-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_tax_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/tax-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxCategories = $this->loadFixturesFromFile('resources/tax_categories.yml');
        $taxCategory  = $taxCategories['tax_category_1'];

        $this->client->request('GET', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_updating_tax_category_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/tax-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_update_tax_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/tax-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_tax_category_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxCategories = $this->loadFixturesFromFile('resources/tax_categories.yml');
        $taxCategory = $taxCategories['tax_category_1'];

        $this->client->request('PUT', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'tax_category/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxCategories = $this->loadFixturesFromFile('resources/tax_categories.yml');
        $taxCategory = $taxCategories['tax_category_1'];

        $data =
<<<EOT
        {
            "name": "Clothing & Accessories",
            "description": "All items classified as clothing with accessories."
        }
EOT;

        $this->client->request('PUT', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_partially_update_tax_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/tax-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_partially_update_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxCategories = $this->loadFixturesFromFile('resources/tax_categories.yml');
        $taxCategory = $taxCategories['tax_category_1'];

        $data =
<<<EOT
        {
            "name": "Clothing & Accessories"
        }
EOT;

        $this->client->request('PATCH', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_tax_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/tax-categories/-1', [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_tax_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $taxCategories = $this->loadFixturesFromFile('resources/tax_categories.yml');
        $taxCategory = $taxCategories['tax_category_1'];

        $this->client->request('DELETE', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'CONTENT_TYPE' => 'application/json',
        ], []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getTaxCategoryUrl($taxCategory), [], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
            'ACCEPT' => 'application/json',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param TaxCategoryInterface $taxCategory
     *
     * @return string
     */
    private function getTaxCategoryUrl(TaxCategoryInterface $taxCategory)
    {
        return 'api/v1/tax-categories/' . $taxCategory->getCode();
    }

}
