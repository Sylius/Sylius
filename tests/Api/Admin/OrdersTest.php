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
use Sylius\Tests\Api\Utils\OrderPlacerTrait;

final class OrdersTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    private const TEST_TOKEN_VALUE = 'nAWw2jewpA';

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();
        $this->setUpAdminContext();
        $this->setUpDefaultGetHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_adjustments_for_order(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder(self::TEST_TOKEN_VALUE);

        $this->requestGet(uri: sprintf('/api/v2/admin/orders/%s/adjustments', self::TEST_TOKEN_VALUE));

        $this->assertResponseSuccessful('admin/order/get_adjustments_for_a_given_order_response');
    }
}
