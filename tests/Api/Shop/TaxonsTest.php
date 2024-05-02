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

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonsTest extends JsonApiTestCase
{
    /** @test */
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

    /** @test */
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

    /** @test */
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
}
