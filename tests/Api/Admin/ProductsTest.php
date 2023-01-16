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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_returns_products_collection(): void
    {
        $this->loadFixturesFromFiles(['product/product_variant_with_original_price.yaml', 'authentication/api_administrator.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/products',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_products_collection_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_returns_product_item(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $fixtures = $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/admin/products/%s', $product->getCode()),
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_product_response',
            Response::HTTP_OK,
        );
    }
}
