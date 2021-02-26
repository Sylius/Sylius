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

final class TaxonTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles(['test/config/fixtures/taxons.yaml']);
        $this->setUpTest();
    }

    /**
     * @test
     */
    public function it_allows_to_get_collection_as_a_visitor(): void
    {
        $response = static::createClient()->request('GET', '/api/v2/shop/taxons');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $objects = json_decode($response->getContent(), true)['hydra:member'];

        $this->assertSame('default', $objects[0]['type']);
    }
}
