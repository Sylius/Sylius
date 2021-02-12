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
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final class FooTest extends ApiTestCase
{
    /** @var string */
    private $JWTUserToken;

    public function setUp(): void
    {
        $files = [
            'test/fixtures/administrator.yaml',
            'test/fixtures/foo_sylius_resource.yaml',
            'test/fixtures/channel.yaml',
        ];

        parent::setUp();
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        /** @var LoaderInterface $loader */
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');

        /** @var JWTTokenManagerInterface $JWTManager */
        $JWTManager = $container->get('lexik_jwt_authentication.jwt_manager');

        $objects = $loader->load($files, [], [], PurgeMode::createDeleteMode());

        $adminUser = $objects['admin'];

        $this->JWTUserToken = $JWTManager->create($adminUser);
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_login_administrator_on_new_not_admin_resource(): void
    {
        $response = static::createClient()->request(
            'GET',
            'api/v2/foo-sylius-resources',
            ['auth_bearer' => $this->JWTUserToken]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertSame('FooSyliusResource1', $objects[0]['name']);
        $this->assertSame('FooSyliusResource2', $objects[1]['name']);
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_visitor(): void
    {
        $response = static::createClient()->request('GET', 'api/v2/foo-sylius-resources');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertSame('FooSyliusResource1', $objects[0]['name']);
        $this->assertSame('FooSyliusResource2', $objects[1]['name']);
    }

    /**
     * @test
     */
    public function it_allows_to_post_as_a_visitor(): void
    {
        $response = static::createClient()->request(
            'POST',
            'api/v2/foo-sylius-resources',
            ['json' => ["name" => "FooSyliusResourcePost"]]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertSame('FooSyliusResourcePost', json_decode($response->getContent(), true)['name']);
    }
}
