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
use Sylius\Bundle\ApiBundle\test\src\Entity\Foo;
use Sylius\Bundle\ApiBundle\test\src\Entity\FooSyliusResource;
use Sylius\Component\Core\Model\AdminUser;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class FooTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['test/config/fixtures/foo.yaml']);
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

        $this->runAssertions($response, 0);
        $this->runAssertions($response, 1);
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_visitor(): void
    {
        $response = static::createClient()->request('GET', 'api/v2/foos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->runAssertions($response, 0);
        $this->runAssertions($response, 1);
    }

    /**
     * @test
     */
    public function it_allows_to_get_item_by_iri(): void
    {
        $response = static::createClient()->request('GET', $this->findIriBy(Foo::class, ['name' => 'Foo0']));

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertSame(json_decode($response->getContent(), true)['name'], 'Foo0');
    }

    /**
     * @test
     */
    public function it_allows_to_post_as_a_visitor(): void
    {
        $fooSyliusResourceIri = $this->findIriBy(FooSyliusResource::class, ['name' => 'FooSyliusResource3']);
        $adminIri = $this->findIriBy(AdminUser::class, ['username' => 'sylius']);

        $response = static::createClient()->request(
            'POST',
            'api/v2/foos',
            ['json' =>
                [
                    "name" => "FooPost",
                    "owner" => $adminIri,
                    "fooSyliusResource" => $fooSyliusResourceIri,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $object = json_decode($response->getContent(), true);

        $this->assertSame('FooPost', $object['name']);
        $this->assertTrue(str_contains($object['owner'], 'api/v2/admin/administrators'));
        $this->assertTrue(str_contains($object['fooSyliusResource'], 'api/v2/foo-sylius-resource'));
    }

    private function runAssertions(ResponseInterface $response, int $objectNumber): void
    {
        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $object1 = $objects[$objectNumber];

        $this->assertSame('Foo' . $objectNumber, $object1['name']);
        $this->assertTrue(str_contains($object1['owner'], 'api/v2/admin/administrators'));
        $this->assertTrue(str_contains($object1['fooSyliusResource'], 'api/v2/foo-sylius-resource'));
    }
}
