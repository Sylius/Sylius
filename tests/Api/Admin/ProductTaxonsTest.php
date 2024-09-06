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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductTaxonsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $fixtures['product_mug_taxon_mugs'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/product-taxons/%s', $productTaxon->getId()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_taxon/get_product_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_product_taxons(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(method: 'GET', uri: '/api/v2/admin/product-taxons', server: $header);

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_taxon/get_product_taxons_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_creates_a_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon_caps'];

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/admin/product-taxons',
            server: $header,
            content: json_encode([
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'taxon' => sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
                'position' => 10,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_taxon/post_product_taxon_response',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_updates_a_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $fixtures['product_cap_taxon_caps'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-taxons/%s', $productTaxon->getId()),
            server: $header,
            content: json_encode([
                'position' => 1,
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_taxon/put_product_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_does_not_update_product_and_taxon_on_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $fixtures['product_cap_taxon_caps'];
        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['taxon_mugs'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/admin/product-taxons/%s', $productTaxon->getId()),
            server: $this->headerBuilder()->withJsonLdContentType()->withJsonLdAccept()->withAdminUserAuthorization('api@example.com')->build(),
            content: json_encode([
                'product' => sprintf('/api/v2/admin/products/%s', $product->getCode()),
                'taxon' => sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product_taxon/put_does_not_update_product_and_taxon_on_product_taxon',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel/channel.yaml', 'product/product_taxon.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $fixtures['product_cap_taxon_caps'];

        $this->client->request(
            method: 'DELETE',
            uri: sprintf('/api/v2/admin/product-taxons/%s', $productTaxon->getId()),
            server: $header,
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
