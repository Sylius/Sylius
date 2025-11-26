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

final class ProductOptionValuesTest extends JsonApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDefaultGetHeaders();
    }

    #[Test]
    public function it_returns_product_option_values(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'product/products_with_options.yaml',
        ]);

        $this->requestGet('/api/v2/shop/product-option-values');

        $this->assertResponseSuccessful('shop/product_option_value/get_product_option_values');
    }

    #[DataProvider('productCodesProvider')]
    #[Test]
    public function it_returns_product_option_values_within_product_with_code(string $productCode): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'product/products_with_options.yaml',
        ]);

        $this->requestGet('/api/v2/shop/product-option-values', [
            'productCode' => $productCode,
        ]);

        $this->assertResponseSuccessful(sprintf(
            'shop/product_option_value/get_product_option_values_of_product_%s',
            strtolower($productCode),
        ));
    }

    #[Test]
    public function it_returns_product_option_value(): void
    {
        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->requestGet('/api/v2/shop/product-options/COLOR/values/COLOR_RED');

        $this->assertResponseSuccessful('shop/product_option_value/get_product_option_value');
    }

    public static function productCodesProvider(): iterable
    {
        yield ['MUG'];
        yield ['CAP'];
    }
}
