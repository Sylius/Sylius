<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Webmozart\Assert\Assert;

final class SyliusConfigMergeTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles([]);

        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_removes_api_method_to_endpoint(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/zones',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @test
     */
    public function it_allows_to_add_new_operation(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path',
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@type' => 'hydra:Collection']);
    }

    /**
     * @test
     */
    public function it_allows_to_add_new_filter(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path?id=20',
        );

        $this->assertJsonContains(['hydra:totalItems' => 0]);

        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path',
        );

        $this->assertJsonContains(['hydra:totalItems' => 1]);
    }

    /**
     * @test
     */
    public function it_merges_configs(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels/WEB',
        );

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_to_overwrite_endpoint(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/orders',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseStatusCodeSame(404);

        static::createClient()->request(
            'GET',
            '/api/v2/admin/orders/get/all',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function it_allows_to_remove_non_crud_endpoint(): void
    {
        $response =
            json_decode(
                static::createClient()
                    ->request(
                    'PATCH',
                    '/api/v2/shop/orders/TOKEN/shipments/TEST'
                    )->getContent(false),
                true
            );

        $this->assertResponseStatusCodeSame(404);
        Assert::contains($response['hydra:description'], 'No route found');
    }
}
