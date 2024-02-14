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

use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductOptionValuesTest extends JsonApiTestCase
{
    /** @test */
    public function it_gets_product_option_value(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $fixtures['product_option_value_color_red'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/product-option-values/%s', $productOptionValue->getCode()),
            server: self::CONTENT_TYPE_HEADER,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/product/get_product_option_value_directly',
            Response::HTTP_OK,
        );
    }
}
