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

namespace Api\Admin;

use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Tests\Api\JsonApiTestCase;

final class ProductOptionValuesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpAdminContext();
    }

    /** @test */
    public function it_gets_a_product_option_value(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_option.yaml',
        ]);

        /** @var ProductOptionInterface $productOption */
        $productOption = $fixtures['product_option_color'];

        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $fixtures['product_option_value_color_blue'];

        $this->requestGet(sprintf('/api/v2/admin/product-options/%s/values/%s', $productOption->getCode(), $productOptionValue->getCode()));

        $this->assertResponseSuccessful('admin/product_option_value/get_product_option_value');
    }
}
