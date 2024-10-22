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

namespace Api\Shop;

use Sylius\Tests\Api\JsonApiTestCase;

final class ProductOptionValuesTest extends JsonApiTestCase
{
    /** @test */
    public function it_returns_product_option_value(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFile('product/product_with_many_locales.yaml');

        $this->requestGet('/api/v2/shop/product-options/COLOR/values/COLOR_RED');

        $this->assertResponseSuccessful('shop/product_option_value/get_product_option_value');
    }
}
