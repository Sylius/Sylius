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
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class PromotionCouponApiTest extends JsonApiTestCase
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
    public function it_does_not_allow_to_show_promotion_coupons_list_when_access_is_denied()
    {
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->loadFixturesFromFile('resources/promotion_coupons.yml');

        $this->client->request('GET', $this->getPromotionCouponsUrl($promotion));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_promotion_coupons()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];
        $this->loadFixturesFromFile('resources/promotion_coupons.yml');

        $this->client->request('GET', $this->getPromotionCouponsUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion_coupon/index_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_show_promotion_when_it_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->client->request('GET', sprintf('/api/v1/promotions/%s/coupons/-1', $promotion->getCode()), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_showing_promotion_coupon()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $promotion_coupons = $this->loadFixturesFromFile('resources/promotion_coupons.yml');
        $coupon = $promotion_coupons['promotionCoupon1'];

        $this->client->request('GET', $this->getPromotionCouponUrl($promotion, $coupon), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion_coupon/show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    function it_does_not_allow_to_create_coupon_for_not_authenticated_users()
    {
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->client->request('POST', $this->getPromotionCouponsUrl($promotion));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    function it_does_not_allow_to_create_coupon_without_specifying_required_data()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $this->client->request('POST', $this->getPromotionCouponsUrl($promotion), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion_coupon/create_validation_fail_response', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    function it_allows_to_create_coupon_with_given_code()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $promotions = $this->loadFixturesFromFile('resources/promotions.yml');
        $promotion = $promotions['promotion2'];

        $data =
<<<EOT
        {
            "code": "1234"
        }
EOT;

        $this->client->request('POST', $this->getPromotionCouponsUrl($promotion), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'promotion_coupon/create_response', Response::HTTP_CREATED);
    }

    /**
     * @param PromotionInterface $promotion
     * @param PromotionCouponInterface|null $coupon
     *
     * @return string
     */
    private function getPromotionCouponUrl(PromotionInterface $promotion, PromotionCouponInterface $coupon = null)
    {
        return sprintf('/api/v1/promotions/%s/coupons/%s', $promotion->getCode(), $coupon->getCode());
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return string
     */
    private function getPromotionCouponsUrl(PromotionInterface $promotion)
    {
        return sprintf('/api/v1/promotions/%s/coupons/', $promotion->getCode());
    }
}
