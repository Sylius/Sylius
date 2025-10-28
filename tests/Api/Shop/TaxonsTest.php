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

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class TaxonsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    #[Test]
    public function it_gets_taxons(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->requestGet('/api/v2/shop/taxons');
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/taxon/get_taxons', Response::HTTP_OK);
    }

    #[Test]
    public function it_gets_a_taxon(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->requestGet('/api/v2/shop/taxons/T_SHIRTS');
        $response = $this->client->getResponse();

        $this->assertResponse($response, 'shop/taxon/get_taxon', Response::HTTP_OK);
    }

    #[Test]
    public function it_returns_nothing_when_trying_to_get_taxonomy_item_that_is_disabled(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->requestGet('/api/v2/shop/taxons/WOMEN_T_SHIRTS');
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    #[Test]
    public function it_preserves_query_param_when_redirecting_from_taxon_slug_to_taxon_code(): void
    {
        $this->loadFixturesFromFile('taxonomy.yaml');

        $this->requestGet('/api/v2/shop/taxons-by-slug/categories/t-shirts?paramName=paramValue');
        $response = $this->client->getResponse();

        $this->assertEquals('/api/v2/shop/taxons/T_SHIRTS?paramName=paramValue', $response->headers->get(('Location')));
        $this->assertResponseCode($response, Response::HTTP_MOVED_PERMANENTLY);
    }

    #[DataProvider('getTaxonsCodes')]
    #[Test]
    public function it_returns_taxon_branch_containing_given_node(string $taxonCode, bool $includeRoot): void
    {
        $this->loadFixturesFromFile('taxon/taxon_tree.yaml');

        $this->requestGet(
            sprintf('/api/v2/shop/taxon-tree/%s/branch', $taxonCode),
            $includeRoot ? ['includeRoot' => 'true'] : [],
        );
        $response = $this->client->getResponse();

        $responseFile = sprintf(
            'shop/taxon/tree/branch/get_for_%s_%s',
            strtolower($taxonCode),
            $includeRoot ? 'with_root' : 'without_root',
        );

        $this->assertResponse($response, $responseFile, Response::HTTP_OK);
    }

    #[DataProvider('getTaxonsCodes')]
    #[Test]
    public function it_returns_taxon_path_to_given_node(string $taxonCode, bool $includeRoot): void
    {
        $this->loadFixturesFromFile('taxon/taxon_tree.yaml');

        $this->requestGet(
            sprintf('/api/v2/shop/taxon-tree/%s/path', $taxonCode),
            $includeRoot ? ['includeRoot' => 'true'] : [],
        );
        $response = $this->client->getResponse();

        $responseFile = sprintf(
            'shop/taxon/tree/path/get_for_%s_%s',
            strtolower($taxonCode),
            $includeRoot ? 'with_root' : 'without_root',
        );

        $this->assertResponse($response, $responseFile, Response::HTTP_OK);
    }

    public static function getTaxonsCodes(): iterable
    {
        $hasRootSuffixes = [
            'with root' => true,
            'without root' => false,
        ];

        foreach ($hasRootSuffixes as $suffix => $hasRoot) {
            yield 'using a leaf ' . $suffix => [
                'taxonCode' => 'PLUGS',
                'includeRoot' => $hasRoot,
            ];

            yield 'using an inter node ' . $suffix => [
                'taxonCode' => 'ACCESSORIES',
                'includeRoot' => $hasRoot,
            ];
            yield 'using root ' . $suffix => [
                'taxonCode' => 'CATEGORY',
                'includeRoot' => $hasRoot,
            ];
        }
    }
}
