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
use Sylius\Bundle\ApiBundle\Application\Entity\Foo;
use Sylius\Bundle\ApiBundle\Application\Entity\FooSyliusResource;
use Sylius\Component\Core\Model\AdminUser;

final class FooTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['Tests/Application/config/fixtures/foo.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_gets_collection_as_a_logged_in_administrator(): void
    {
        static::createClient()->request(
            'GET',
            'api/v2/foos',
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Foo',
            '@id' => '/api/v2/foos',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@type' => 'Foo',
                'name' => 'Foo1',
                'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
                'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
            ], [
                '@type' => 'Foo',
                'name' => 'Foo2',
                'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
                'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
            ]],
            'hydra:totalItems' => 2,
        ]);
    }

    /**
     * @test
     */
    public function it_gets_collection_as_a_visitor(): void
    {
        static::createClient()->request('GET', 'api/v2/foos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Foo',
            '@id' => '/api/v2/foos',
            '@type' => 'hydra:Collection',
            'hydra:member' => [[
                '@type' => 'Foo',
                'name' => 'Foo1',
                'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
                'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
            ], [
                '@type' => 'Foo',
                'name' => 'Foo2',
                'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
                'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
            ]],
            'hydra:totalItems' => 2,
        ]);
    }

    /**
     * @test
     */
    public function it_gets_an_item_as_a_vistor(): void
    {
        /** @var Foo $foo */
        $foo = $this->objects['foo1'];

        static::createClient()->request('GET', 'api/v2/foos/' . $foo->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Foo',
            '@type' => 'Foo',
            'name' => 'Foo1',
            'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
            'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
        ]);
    }

    /**
     * @test
     */
    public function it_gets_an_item_as_a_logged_in_administrator_by_admin_endpoint(): void
    {
        /** @var Foo $foo */
        $foo = $this->objects['foo1'];

        static::createClient()->request(
            'GET',
            'api/v2/admin/foos/' . $foo->getId(),
            ['auth_bearer' => $this->JWTAdminUserToken],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Foo',
            '@type' => 'Foo',
            'name' => 'Foo1',
            'owner' => $this->findIriBy(AdminUser::class, ['username' => 'sylius']),
            'fooSyliusResource' => $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']),
        ]);
    }

    /**
     * @test
     */
    public function it_does_not_get_an_item_as_a_visitor_by_admin_endpoint(): void
    {
        /** @var Foo $foo */
        $foo = $this->objects['foo1'];

        static::createClient()->request('GET', 'api/v2/admin/foos/' . $foo->getId());

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains(['message' => 'JWT Token not found']);
    }

    /**
     * @test
     */
    public function it_creates_a_new_entity_as_a_visitor(): void
    {
        $fooSyliusResourceIri = $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource']);
        $adminUserIri = $this->findIriBy(AdminUser::class, ['username' => 'sylius']);

        static::createClient()->request('POST', 'api/v2/foos', ['json' => [
            'name' => 'FooNew',
            'owner' => $adminUserIri,
            'fooSyliusResource' => $fooSyliusResourceIri,
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/v2/contexts/Foo',
            '@type' => 'Foo',
            'name' => 'FooNew',
            'owner' => $adminUserIri,
            'fooSyliusResource' => $fooSyliusResourceIri,
        ]);
    }
}
