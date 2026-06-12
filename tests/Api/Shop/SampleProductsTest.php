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

final class SampleProductsTest extends JsonApiTestCase
{
    #[Test]
    public function it_generates_shop_iris_for_resource_subclass_instances(): void
    {
        $this->client->request(
            method: 'GET',
            uri: '/api/v2/shop/sample-products',
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/sample_product/get_sample_products_response',
            Response::HTTP_OK,
        );
    }
}
