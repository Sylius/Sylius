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

use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductTaxonsTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_a_product_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['channel.yaml', 'product/product_taxon.yaml']);

        /** @var ProductTaxonInterface $productTaxon */
        $productTaxon = $fixtures['product_mug_taxon_mugs'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-taxons/%s', $productTaxon->getId()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product_taxon/get_product_taxon_response',
            Response::HTTP_OK,
        );
    }
}
