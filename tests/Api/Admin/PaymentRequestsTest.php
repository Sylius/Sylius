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

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Tests\Api\JsonApiTestCase;

final class PaymentRequestsTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        $this->setUpAdminContext();

        parent::setUp();
    }

    /** @test */
    public function it_gets_payment_requests_for_payment(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
            'payment_request/payment_request.yaml',
            'payment_request/order.yaml',
        ]);

        /** @var PaymentInterface $payment */
        $payment = $fixtures['payment'];

        $this->requestGet(uri: sprintf('/api/v2/admin/payments/%s/payment-requests', $payment->getId()));

        $this->assertResponseSuccessful('admin/payment_request/get_payment_requests_for_payment');
    }

    /** @test */
    public function it_gets_a_payment_request(): void
    {
        $this->setUpDefaultGetHeaders();

        $fixtures = $this->loadFixturesFromFiles([
            'authentication/api_administrator.yaml',
            'channel.yaml',
            'payment_method.yaml',
            'payment_request/payment_request.yaml',
            'payment_request/order.yaml',
        ]);

        /** @var PaymentRequestInterface $paymentRequest */
        $paymentRequest = $fixtures['payment_request_authorize'];

        $this->requestGet(uri: sprintf('/api/v2/admin/payment-requests/%s', $paymentRequest->getHash()));

        $this->assertResponseSuccessful('admin/payment_request/get_payment_request');
    }
}
