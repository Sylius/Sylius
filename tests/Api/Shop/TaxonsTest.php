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

namespace Sylius\Tests\Api\Shop;

use PHPUnit\Framework\Attributes\Test;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonsTest extends JsonApiTestCase
{
    #[Test]
    public function it_gets_taxons(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/taxons',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/taxon/get_taxons', Response::HTTP_OK);
    }

    #[Test]
    public function it_gets_a_taxon(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/taxons/T_SHIRTS',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/taxon/get_taxon', Response::HTTP_OK);
    }

    #[Test]
    public function it_returns_nothing_when_trying_to_get_taxonomy_item_that_is_disabled(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/taxons/WOMEN_T_SHIRTS',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function it_preserves_query_param_when_redirecting_from_taxon_slug_to_taxon_code(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/taxons-by-slug/categories/t-shirts?paramName=paramValue',
            server: self::CONTENT_TYPE_HEADER,
        );
        $response = $this->client->getResponse();

        $this->assertEquals('/api/v2/shop/taxons/T_SHIRTS?paramName=paramValue', $response->headers->get(('Location')));
        $this->assertResponseCode($response, Response::HTTP_MOVED_PERMANENTLY);
    }
}
