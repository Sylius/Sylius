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
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_product(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product.yaml']);
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
            'admin/product/get_product_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_products(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'product/product.yaml']);
        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'GET',
            uri: '/api/v2/admin/products',
            server: $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/product/get_products_response',
            Response::HTTP_OK,
        );
    }
}
