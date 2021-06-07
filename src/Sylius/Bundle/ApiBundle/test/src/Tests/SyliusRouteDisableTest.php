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

class SyliusRouteDisableTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_has_removed_api_endpoint(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/promotions',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseStatusCodeSame(404);
    }
}
