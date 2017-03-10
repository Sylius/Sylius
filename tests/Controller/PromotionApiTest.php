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
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class PromotionApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_promotions_list_when_access_is_denied()
    {
        $this->loadFixturesFromFile('resources/promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_promotions()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_promotions_by_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');
        $this->loadFixturesFromFile('resources/many_promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', ['sorting' => ['code' => 'asc']], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/sorted_by_code_index_response');
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_promotions_by_name()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');
        $this->loadFixturesFromFile('resources/many_promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', ['sorting' => ['name' => 'desc']], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/sorted_by_name_index_response');
    }

    /**
     * @test
     */
    public function it_allows_sorting_the_index_of_promotions_by_priority()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');
        $this->loadFixturesFromFile('resources/many_promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', ['sorting' => ['priority' => 'asc']], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/sorted_by_priority_index_response');
    }

    /**
     * @test
     */
    public function it_allows_to_get_promotions_list_filtered_by_name_and_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');
        $this->loadFixturesFromFile('resources/many_promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', ['criteria' => ['search' => 'promo']], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/filtered_by_code_and_name_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_to_get_list_of_coupon_based_promotions()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/promotions.yml');
        $this->loadFixturesFromFile('resources/many_promotions.yml');

        $this->client->request('GET', '/api/v1/promotions/', ['criteria' => ['couponBased' => 'true']], [], [
            'HTTP_Authorization' => 'Bearer SampleTokenNjZkNjY2MDEwMTAzMDkxMGE0OTlhYzU3NzYyMTE0ZGQ3ODcyMDAwM2EwMDZjNDI5NDlhMDdlMQ',
        ]);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/only_coupon_based_index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_promotion_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', '/api/v1/promotions/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->client->request('GET', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_delete_promotion_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('DELETE', '/api/v1/promotions/-1', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_delete_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion1'];

        $this->client->request('DELETE', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_create_promotion_without_required_fields()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, []);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_create_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion"
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_promotion_with_channels()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "channels": [
                "WEB",
                "MOB"
            ]
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_response_with_channels', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_promotion_with_time_of_duration()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "startsAt": {
                "date": "2017-12-05",
                "time": "11:00"
            },
            "endsAt": {
                "date": "2017-12-31",
                "time": "11:00"
            }
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_response_with_time_of_duration', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_promotion_with_rules()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "rules": [
                {
                    "type": "nth_order",
                    "configuration": {
                        "nth": 3
                    }
                },
                {
                    "type": "item_total",
                    "configuration": {
                        "WEB": {
                            "amount": 12
                        },
                        "MOB": {
                            "amount": 15
                        }
                    }
                }
            ]
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_response_with_rules', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_promotion_with_actions()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/channels.yml');
        $this->loadFixturesFromFile('resources/products.yml');
        $this->loadFixturesFromFile('resources/product_taxons.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "actions": [
                {
                    "type": "unit_percentage_discount",
                    "configuration": {
                        "WEB": {
                            "percentage": 15,
                            "filters": {
                                "price_range_filter": {
                                    "min": 1,
                                    "max": 12000
                                },
                                "taxons_filter": {
                                    "taxons": "mugs"
                                },
                                "products_filter": {
                                    "products": "MUG_SW,MUG_LOTR"
                                }
                            }
                        },
                        "MOB": {
                            "percentage": 20,
                            "filters": {
                                "products_filter": {
                                    "products": "MUG_SW"
                                }
                            }
                        }
                    }
                },
                {
                    "type": "order_fixed_discount",
                    "configuration": {
                        "WEB": {
                            "amount": 12
                        },
                        "MOB": {
                            "amount": 15
                        }
                    }
                }
            ]
        }
EOT;

        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_response_with_actions', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_coupon_based_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "couponBased": true
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_coupon_based_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_to_create_exclusive_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $data =
<<<EOT
        {
            "code": "christmas-promotion",
            "name": "Christmas Promotion",
            "exclusive": true,
            "priority": 0
        }
EOT;
        $this->client->request('POST', '/api/v1/promotions/', [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/create_exclusive_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_updating_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions["promotion1"];

        $data =
<<<EOT
        {
            "name": "Monday promotion",
            "priority": 0
        }
EOT;
        $this->client->request('PUT', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/show_response_after_update', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_promotion()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions["promotion1"];

        $data =
<<<EOT
        {
            "exclusive": true
        }
EOT;
        $this->client->request('PATCH', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithContentType, $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getPromotionUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion/show_response_after_partial_update', Response::HTTP_OK);
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return string
     */
    private function getPromotionUrl(PromotionInterface $promotion)
    {
        return '/api/v1/promotions/' . $promotion->getCode();
    }
}
