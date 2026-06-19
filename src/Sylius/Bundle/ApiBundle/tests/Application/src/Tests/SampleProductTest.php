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

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

final class SampleProductTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles([]);
        $this->setUpTest();
    }

    #[Test]
    public function it_generates_correct_iris_for_subclass_instances_on_admin_endpoint(): void
    {
        static::createClient()->request(
            'GET',
            'api/v2/admin/sample-products',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/SampleProduct',
            '@id' => '/api/v2/admin/sample-products',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@id' => '/api/v2/admin/sample-products/1',
                '@type' => 'SampleProduct',
                'name' => 'Special Product',
            ]],
        ]);
    }

    #[Test]
    public function it_generates_correct_iris_for_subclass_instances_on_shop_endpoint(): void
    {
        static::createClient()->request('GET', 'api/v2/shop/sample-products');

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/SampleProduct',
            '@id' => '/api/v2/shop/sample-products',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@id' => '/api/v2/shop/sample-products/1',
                '@type' => 'SampleProduct',
                'name' => 'Special Product',
            ]],
        ]);
    }
}
