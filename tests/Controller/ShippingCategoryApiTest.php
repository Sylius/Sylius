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
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShippingCategoryApiTest extends JsonApiTestCase
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
        'Accept' => 'application/json',
    ];

    /**
     * @test
     */
    public function it_denies_creating_shipping_category_for_non_authenticated_user()
    {
        $this->client->request('POST', '/api/v1/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_shipping_category_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/shipping-categories/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "reg",
            "name": "Regular",
            "description": "Regular weight items"
        }
EOT;

        $this->client->request('POST', '/api/v1/shipping-categories/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_denies_getting_shipping_categories_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/shipping-categories/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_shipping_categories_list()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/shipping_categories.yml');

        $this->client->request('GET', '/api/v1/shipping-categories/', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_shipping_category_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_shipping_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/shipping-categories/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $shippingCategories['shipping_category_2'];

        $this->client->request('GET', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_updating_shipping_category_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/shipping-categories/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_updating_shipping_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/shipping-categories/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_update_shipping_category_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $shippingCategories['shipping_category_1'];

        $this->client->request('PUT', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_update_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $shippingCategories['shipping_category_1'];

        $data =
<<<EOT
        {
            "name": "Light",
            "description": "Light weight items"
        }
EOT;

        $this->client->request('PUT', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_partially_updating_shipping_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PATCH', '/api/v1/shipping-categories/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_partially_update_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $shippingCategories['shipping_category_1'];

        $data =
<<<EOT
        {
            "name": "Light",
            "description": "Light weight items"
        }
EOT;

        $this->client->request('PATCH', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipping_category/update_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_trying_to_delete_shipping_category_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/shipping-categories/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_shipping_category()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $shippingCategories = $this->loadFixturesFromFile('resources/shipping_categories.yml');

        /** @var ShippingCategoryInterface $shippingCategory */
        $shippingCategory = $shippingCategories['shipping_category_2'];

        $this->client->request('DELETE', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getShippingCategoryUrl($shippingCategory), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @param ShippingCategoryInterface $shippingCategory
     *
     * @return string
     */
    private function getShippingCategoryUrl(ShippingCategoryInterface $shippingCategory)
    {
        return '/api/v1/shipping-categories/' . $shippingCategory->getCode();
    }
}
