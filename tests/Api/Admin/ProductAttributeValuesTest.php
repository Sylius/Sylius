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

use Sylius\Tests\Api\JsonApiTestCase;

final class ProductAttributeValuesTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();
    }

    /** @test */
    public function it_gets_attribute_value(): void
    {
        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'product/product_attribute.yaml',
        ]);
        $attributeValue = $fixtures['product_attribute_value_checkbox'];

        $this->requestGet('/api/v2/admin/product-attribute-values/' . $attributeValue->getId());

        $this->assertResponse($this->client->getResponse(), 'admin/product/get_product_attribute_value');
    }
}
