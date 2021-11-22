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

namespace Sylius\Tests\Api\Shop;

use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductsTest extends JsonApiTestCase
{
    /** @test */
    public function it_preserves_query_param_when_redirecting_from_product_slug_to_product_code(): void
    {
        $this->loadFixturesFromFile('product_variant_with_original_price.yaml');

        $this->client->request('GET', '/api/v2/shop/products-by-slug/mug?paramName=paramValue', [], [], self::CONTENT_TYPE_HEADER);
        $response = $this->client->getResponse();

        $this->assertEquals('/api/v2/shop/products/MUG?paramName=paramValue', $response->headers->get(('Location')));
        $this->assertResponseCode($response, Response::HTTP_MOVED_PERMANENTLY);
    }
}
