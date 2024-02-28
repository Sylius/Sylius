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
use Symfony\Component\HttpFoundation\Response;

final class PaymentsTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();
        $this->setUpAdminContext();
        $this->setUpDefaultHeaders();

        parent::setUp();
    }

    /** @test */
    public function it_gets_payments(): void
    {
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

        $this->assertResponseSuccessful('admin/payment/get_payments_response');
    }

    /** @test */
    public function it_gets_payments_of_the_specific_order(): void
    {
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

        $this->assertResponseSuccessful('admin/payment/get_payment_response');
    }

    /** @test */
    public function it_completes_payment(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('nAWw2jewpA');

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PATCH',
            uri: sprintf('/api/v2/admin/payments/%s/complete', $order->getPayments()->first()->getId()),
            server: $header,
        );

        $this->assertResponse($this->client->getResponse(), 'admin/payment/patch_complete_payment_response', Response::HTTP_OK);
    }
}
