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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class DisablingApiTest extends ApiTestCase
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
    public function it_gets_collection_with_shop_prefix(): void
    {
        static::createClient()->request(
            'GET',
            'api/v2/admin/orders',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function it_returns_route_not_found_with_api_disabled(): void
    {
        $this->disableApi();

        static::createClient()->request(
            'GET',
            'api/v2/admin/orders',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseStatusCodeSame(404);

        $this->enableApi();

        static::createClient()->request(
            'GET',
            'api/v2/admin/orders',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseIsSuccessful();
    }

    private function disableApi(): void
    {
        $_ENV['SYLIUS_API_ENABLED'] = false;
    }

    private function enableApi(): void
    {
        $_ENV['SYLIUS_API_ENABLED'] = true;
    }
}
