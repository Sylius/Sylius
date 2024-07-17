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
use Sylius\Bundle\ApiBundle\Application\Entity\FooSyliusResource;

final class FooSyliusResourceTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['Tests/Application/config/fixtures/foo_sylius_resource.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_gets_a_collection_as_a_logged_in_administrator(): void
    {
        static::createClient()->request(
            'GET',
            'api/v2/foo-sylius-resources',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/FooSyliusResource',
            '@id' => '/api/v2/foo-sylius-resources',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@type' => 'FooSyliusResource',
                'name' => 'FooSyliusResource1',
            ], [
                '@type' => 'FooSyliusResource',
                'name' => 'FooSyliusResource2',
            ]],
        ]);
    }

    /**
     * @test
     */
    public function it_gets_a_collection_as_a_visitor(): void
    {
        static::createClient()->request('GET', 'api/v2/foo-sylius-resources');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/FooSyliusResource',
            '@id' => '/api/v2/foo-sylius-resources',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@type' => 'FooSyliusResource',
                'name' => 'FooSyliusResource1',
            ], [
                '@type' => 'FooSyliusResource',
                'name' => 'FooSyliusResource2',
            ]],
        ]);
    }

    /**
     * @test
     */
    public function it_creates_a_new_entity_as_a_visitor(): void
    {
        static::createClient()->request(
            'POST',
            'api/v2/foo-sylius-resources',
            ['json' => ['name' => 'FooSyliusResourcePost']],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/FooSyliusResource',
            '@type' => 'FooSyliusResource',
            'name' => 'FooSyliusResourcePost',
        ]);
    }

    /**
     * @test
     */
    public function it_gets_an_item_as_a_logged_in_administrator(): void
    {
        $fooSyliusResourceIri = $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource1']);

        static::createClient()->request(
            'GET',
            $fooSyliusResourceIri,
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/FooSyliusResource',
            '@id' => $fooSyliusResourceIri,
            '@type' => 'FooSyliusResource',
            'name' => 'FooSyliusResource1',
        ]);
    }
}
