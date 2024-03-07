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

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class PaymentRequestsTest extends JsonApiTestCase
{
    use ShopUserLoginTrait;
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /** @test */
    public function it_gets_a_payment_request(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'payment_method.yaml',
            'payment_request/payment_request.yaml',
            'payment_request/order_with_customer.yaml',
        ]);

        /** @var PaymentRequestInterface $paymentRequest */
        $paymentRequest = $fixtures['payment_request_capture'];

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v2/shop/payment-requests/%s', $paymentRequest->getHash()),
            server: $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withShopUserAuthorization('oliver@doe.com')->build(),
        );

        $this->assertResponseSuccessful('shop/payment_request/get_payment_request');
    }

    /** @test */
    public function it_creates_a_payment_request(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $order = $this->placeOrder('nAWw2jewpA', 'oliver@doe.com');
        $payment = $order->getLastPayment();

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/payment-requests',
            server: $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'paymentId' => sprintf('/api/v2/shop/payments/%s', $payment->getId()),
                'paymentMethodCode' => $payment->getMethod()->getCode(),
                'action' => 'capture',
                'payload' => [
                    'target_path' => 'https://myshop.tld/target-path',
                    'after_path' => 'https://myshop.tld/after-path'
                ],
            ], \JSON_THROW_ON_ERROR)
        );


        $this->assertResponse(
            $this->client->getResponse(),
            'shop/payment_request/post_payment_request',
            Response::HTTP_CREATED,
        );
    }

    /** @test */
    public function it_does_not_create_a_payment_request_without_required_data(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'cart.yaml',
            'country.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        $this->placeOrder('nAWw2jewpA', 'oliver@doe.com');

        $this->client->request(
            method: 'POST',
            uri: '/api/v2/shop/payment-requests',
            server: $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([], \JSON_THROW_ON_ERROR)
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/payment_request/post_payment_request_without_required_data',
            Response::HTTP_BAD_REQUEST,
        );
    }

    /** @test */
    public function it_updates_a_payment_request(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'payment_method.yaml',
            'payment_request/payment_request.yaml',
            'payment_request/order_with_customer.yaml',
        ]);

        /** @var PaymentRequestInterface $paymentRequest */
        $paymentRequest = $fixtures['payment_request_capture'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/payment-requests/%s', $paymentRequest->getHash()),
            server: $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'payload' => [
                    'target_path' => 'https://myshop.tld/new-target-path',
                    'after_path' => 'https://myshop.tld/new-after-path'
                ],
            ], \JSON_THROW_ON_ERROR)
        );

        $this->assertResponseSuccessful('shop/payment_request/put_payment_request');
    }

    /** @test */
    public function it_does_not_update_a_payment_request_in_wrong_state(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/customer.yaml',
            'channel.yaml',
            'payment_method.yaml',
            'payment_request/payment_request.yaml',
            'payment_request/order_with_customer.yaml',
        ]);

        /** @var PaymentRequestInterface $paymentRequest */
        $paymentRequest = $fixtures['payment_request_authorize'];

        $this->client->request(
            method: 'PUT',
            uri: sprintf('/api/v2/shop/payment-requests/%s', $paymentRequest->getHash()),
            server: $this->headerBuilder()->withJsonLdAccept()->withJsonLdContentType()->withShopUserAuthorization('oliver@doe.com')->build(),
            content: json_encode([
                'payload' => [
                    'target_path' => 'https://myshop.tld/new-target-path',
                    'after_path' => 'https://myshop.tld/new-after-path'
                ],
            ], \JSON_THROW_ON_ERROR)
        );
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
