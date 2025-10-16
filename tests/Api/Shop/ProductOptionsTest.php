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

final class ProductOptionsTest extends JsonApiTestCase
{
    #[Test]
    public function it_returns_product_options(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'product/products_with_options.yaml',
        ]);

        $this->requestGet('/api/v2/shop/product-options');

        $this->assertResponseSuccessful('shop/product_option/get_product_options');
    }

    #[DataProvider('productCodesProvider')]
    #[Test]
    public function it_returns_product_options_within_product_with_code(string $productCode): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'product/products_with_options.yaml',
        ]);

        $this->requestGet('/api/v2/shop/product-options', [
            'productCode' => $productCode,
        ]);

        $this->assertResponseSuccessful(sprintf(
            'shop/product_option/get_product_options_of_product_%s',
            strtolower($productCode),
        ));
    }

    #[Test]
    public function it_returns_product_option(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->requestGet('/api/v2/shop/product-options/COLOR');

        $this->assertResponseSuccessful('shop/product_option/get_product_option');
    }

    public static function productCodesProvider(): iterable
    {
        yield ['MUG'];
        yield ['CAP'];
    }
}
