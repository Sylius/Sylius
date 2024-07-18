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

final class PaymentsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();
        $this->setUpAdminContext();

        parent::setUp();
    }

    /** @test */
    public function it_gets_payments(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('nAWw2jewpA');

        $this->requestGet(uri: '/api/v2/admin/payments');

        $this->assertResponseSuccessful('admin/payment/get_payments');
    }

    /** @test */
    public function it_gets_payments_filtered_by_state(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('paidOrder');
        $this->payOrder($order);

        $this->placeOrder('unpaidOrder');

        $this->requestGet(uri: '/api/v2/admin/payments', queryParameters: ['state' => 'new']);

        $this->assertResponseSuccessful('admin/payment/get_payments_filtered_by_state');
    }

    /** @test */
    public function it_gets_payments_of_the_specific_order(): void
    {
        $this->setUpDefaultGetHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('nAWw2jewpA');

        $this->requestGet(uri: '/api/v2/admin/orders/nAWw2jewpA');
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->requestGet(uri: '/api/v2/admin/payments/' . $orderResponse['payments'][0]['id']);

        $this->assertResponseSuccessful('admin/payment/get_payment');
    }

    /** @test */
    public function it_completes_payment(): void
    {
        $this->setUpDefaultPatchHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('nAWw2jewpA');

        $this->requestPatch(uri: sprintf('/api/v2/admin/payments/%s/complete', $order->getPayments()->first()->getId()));

        $this->assertResponseSuccessful('admin/payment/patch_complete_payment');
    }

    /** @test */
    public function it_does_not_complete_payment_if_it_is_not_in_the_new_state(): void
    {
        $this->setUpDefaultPatchHeaders();

        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('nAWw2jewpA');

        $this->payOrder($order);
        $this->requestPatch(uri: sprintf('/api/v2/admin/payments/%s/complete', $order->getPayments()->first()->getId()));

        $this->assertResponseUnprocessableEntity('admin/payment/patch_not_complete_payment');
    }
}
