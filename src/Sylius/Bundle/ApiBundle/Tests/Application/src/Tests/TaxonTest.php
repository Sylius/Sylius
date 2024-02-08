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

final class TaxonTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['Tests/Application/config/fixtures/taxons.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_gets_collection_with_shop_iris_as_a_visitor(): void
    {
        static::createClient()->request('GET', '/api/v2/shop/taxons');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Taxon',
            '@id' => '/api/v2/shop/taxons',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@id' => '/api/v2/shop/taxons/mugs',
                '@type' => 'Taxon',
                'type' => 'default',
                'code' => 'mugs',
                'translations' => [],
            ], [
                '@id' => '/api/v2/shop/taxons/stickers',
                '@type' => 'Taxon',
                'type' => 'default',
                'code' => 'stickers',
                'translations' => [],
            ]],
        ]);
    }

    /**
     * @test
     */
    public function it_gets_collection_with_admin_iris_as_a_logged_in_administrator(): void
    {
        static::createClient()->request(
            'GET',
            '/api/v2/admin/taxons',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Taxon',
            '@id' => '/api/v2/admin/taxons',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@id' => '/api/v2/admin/taxons/category',
                '@type' => 'Taxon',
                'type' => 'default',
                'code' => 'category',
                'translations' => [],
            ], [
                '@id' => '/api/v2/admin/taxons/mugs',
                '@type' => 'Taxon',
                'type' => 'default',
                'code' => 'mugs',
                'translations' => [],
            ], [
                '@id' => '/api/v2/admin/taxons/stickers',
                '@type' => 'Taxon',
                'type' => 'default',
                'code' => 'stickers',
                'translations' => [],
            ]],
        ]);
    }
}
