<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\test\tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

final class PromotionTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['test/fixtures/promotions.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_visitor_on_resource_from_api_bundle_with_customized_path(): void
    {
        $response = static::createClient()->request('GET', '/api/v2/custom/promotions');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];
        $this->assertSame('Sunday promotion', $objects[0]['name']);
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_an_login_administrator_on_resource_from_api_bundle_with_customized_path(): void
    {
        $response = static::createClient()->request(
            'GET',
            '/api/v2/custom/promotions',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];
        $this->assertSame('Sunday promotion', $objects[0]['name']);
    }
}
