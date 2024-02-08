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

use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ProductTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_returns_products_collection(): void
    {
        $this->loadFixturesFromFiles(['product/product_variant_with_original_price.yaml', 'authentication/api_administrator.yaml']);

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        $this->client->request('GET',
            '/api/v2/admin/products',
            [],
            [],
            $header
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_products_collection_response',
            Response::HTTP_OK
        );
    }

    /** @test */
    public function it_returns_product_item(): void
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yaml');
        $fixtures = $this->loadFixturesFromFile('product/product_variant_with_original_price.yaml');

        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;
        $header = array_merge($header, self::CONTENT_TYPE_HEADER);

        /** @var ProductInterface $product */
        $product = $fixtures['product_mug'];
        $this->client->request('GET',
            sprintf('/api/v2/admin/products/%s', $product->getCode()),
            [],
            [],
            $header
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/get_product_response',
            Response::HTTP_OK
        );
    }
}
