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

final class FooTest extends ApiTestCase
{
    use Test;

    public function setUp(): void
    {
        $this->setFixturesFiles(['test/fixtures/foo.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_login_administrator_on_new_not_admin_resource(): void
    {
        $response = static::createClient()->request(
            'GET',
            'api/v2/foos',
            ['auth_bearer' => $this->JWTAdminUserToken]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertSame('Foo1', $objects[0]['name']);
        $this->assertSame('Foo2', $objects[1]['name']);
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_visitor(): void
    {
        $response = static::createClient()->request('GET', 'api/v2/foos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertSame('Foo1', $objects[0]['name']);
        $this->assertSame('Foo2', $objects[1]['name']);

    }

    /**
     * @test
     */
    public function it_allows_to_post_as_a_visitor(): void
    {
        $response = static::createClient()->request(
            'POST',
            'api/v2/foos',
            ['json' => ["name" => "FooPost"]]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertSame('FooPost', json_decode($response->getContent(), true)['name']);
    }
}
