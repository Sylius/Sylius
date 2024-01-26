<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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

    /** @test */
    public function it_removes_api_method_to_endpoint_with_yaml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/zones',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseStatusCodeSame(404);
    }

    /** @test */
    public function it_removes_api_method_to_endpoint_with_xml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/countries',
        );

        self::assertResponseStatusCodeSame(404);
    }

    /** @test */
    public function it_allows_to_add_new_operation_with_yaml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['@type' => 'hydra:Collection']);
    }

    /** @test */
    public function it_allows_to_add_new_operation_with_xml(): void
    {
        static::createClient()->request(
            'DELETE',
            '/api/v2/admin/countries/US',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_allows_to_add_new_filter_with_yaml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path?id=20',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['hydra:totalItems' => 0]);

        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels-new-path',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['hydra:totalItems' => 1]);
    }

    /** @test */
    public function it_allows_to_add_new_filter_with_xml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/updated/countries?id=42',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['hydra:totalItems' => 0]);

        static::createClient()->request(
            'GET',
            '/api/v2/admin/updated/countries',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['hydra:totalItems' => 1]);
    }

    /** @test */
    public function it_merges_configs_with_yaml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/channels/WEB',
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_merges_configs_with_xml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/countries',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_allows_to_overwrite_endpoint_with_yaml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/orders',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseStatusCodeSame(404);

        static::createClient()->request(
            'GET',
            '/api/v2/admin/orders/get/all',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_allows_to_overwrite_endpoint_with_xml(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/shop/countries/US',
        );

        self::assertResponseStatusCodeSame(404);

        static::createClient()->request(
            'GET',
            '/api/v2/shop/countries/new/US',
        );

        self::assertResponseIsSuccessful();
    }

    /** @test */
    public function it_allows_to_remove_non_crud_endpoint_with_yaml(): void
    {
        $response = json_decode(
            static::createClient()
                ->request(
                    'PATCH',
                    '/api/v2/shop/orders/TOKEN/shipments/TEST',
                )->getContent(false),
            true,
            512,
            \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(404);
        Assert::contains($response['hydra:description'], 'No route found');
    }
}
