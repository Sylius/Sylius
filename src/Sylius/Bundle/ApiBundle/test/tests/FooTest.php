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

final class FooTest extends ApiTestCase
{
    /** @var EntityManagerInterface|null */
    private $entityManager;

    public function setUp(): void
    {
        $files = [
            'test/fixtures/administrator.yaml',
            'test/fixtures/foo.yaml',
        ];

        parent::setUp();
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        /** @var LoaderInterface $loader */
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');
        $objects = $loader->load($files);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        foreach ($objects as $object) {
            $this->entityManager->persist($object);
        }
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection(): void
    {
        static::createClient()->request('GET', 'api/v2/foos');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJson(
<<<EOT
{
    "@context":"\/api\/v2\/contexts\/Foo",
    "@id":"\/api\/v2\/foos",
    "@type":"hydra:Collection",
    "hydra:member":[
        {
            "@id":"\/api\/v2\/foos\/2",
            "@type":"Foo",
            "id":2,
            "name":"Foo2"
        },
        {
            "@id":"\/api\/v2\/foos\/1",
            "@type":"Foo",
            "id":1,
            "name":"Foo1"
        }
    ],
    "hydra:totalItems":2
}
EOT
        );
    }

    /**
     * @test
     */
    public function it_allows_to_post(): void
    {
        static::createClient()->request('POST', 'api/v2/foos', ['json' => ["name" => "foo"]]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJson(
<<<EOT
{
    "@context":"\/api\/v2\/contexts\/Foo",
    "@id":"\/api\/v2\/foos\/2",
    "@type":"Foo",
    "id":2,
    "name":"foo"
}
EOT
        );
    }
}
