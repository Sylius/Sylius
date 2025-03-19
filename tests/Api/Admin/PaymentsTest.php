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
        $this->setUpAdminContext();

        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPatchHeaders();

        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_payments(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder();

        $this->requestGet(uri: '/api/v2/admin/payments');

        $this->assertResponseSuccessful('admin/payment/get_payments');
    }

    /** @test */
    public function it_gets_payments_filtered_by_state(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
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
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('token');

        $this->requestGet(uri: '/api/v2/admin/orders/token');
        $orderResponse = json_decode($this->client->getResponse()->getContent(), true);

        $this->requestGet(uri: '/api/v2/admin/payments/' . $orderResponse['payments'][0]['id']);

        $this->assertResponseSuccessful('admin/payment/get_payment');
    }

    /** @test */
    public function it_completes_payment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->requestPatch(uri: sprintf('/api/v2/admin/payments/%s/complete', $order->getPayments()->first()->getId()));

        $this->assertResponseSuccessful('admin/payment/patch_complete_payment');
    }

    /** @test */
    public function it_refunds_the_payment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->fulfillOrder('token');

        $this->requestPatch(uri: sprintf('/api/v2/admin/payments/%s/refund', $order->getPayments()->first()->getId()));

        $this->assertResponseSuccessful('admin/payment/patch_refund_payment');
    }

    /** @test */
    public function it_does_not_refund_the_payment_if_it_is_not_fulfilled(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');

        $this->requestPatch(sprintf('/api/v2/admin/payments/%s/refund', $order->getPayments()->first()->getId()));

        $this->assertResponseErrorMessage('Transition "refund" cannot be applied on "new" payment.');
    }

    /** @test */
    public function it_does_not_refund_the_payment_if_it_is_already_refunded(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->fulfillOrder('token');
        $order = $this->refundOrder($order);

        $this->requestPatch(sprintf('/api/v2/admin/payments/%s/refund', $order->getPayments()->first()->getId()));

        $this->assertResponseErrorMessage('Transition "refund" cannot be applied on "refunded" payment.');
    }

    /** @test */
    public function it_does_not_refund_the_payment_if_it_is_cancelled(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');
        $this->cancelOrder('token');

        $this->requestPatch(sprintf('/api/v2/admin/payments/%s/refund', $order->getPayments()->first()->getId()));

        $this->assertResponseErrorMessage('Transition "refund" cannot be applied on "cancelled" payment.');
    }

    /** @test */
    public function it_does_not_complete_payment_if_it_is_not_in_the_new_state(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('token');
        $this->payOrder($order);

        $this->requestPatch(sprintf('/api/v2/admin/payments/%s/complete', $order->getPayments()->first()->getId()));

        $this->assertResponseErrorMessage('Transition "complete" cannot be applied on "completed" payment.');
    }
}
