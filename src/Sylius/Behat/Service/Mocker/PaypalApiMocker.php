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

namespace Sylius\Behat\Service\Mocker;

use Mockery\MockInterface;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Behat\Service\ResponseLoaderInterface;

class PaypalApiMocker
{
    public function __construct(
        private MockerInterface $mocker,
        private ResponseLoaderInterface $responseLoader,
    ) {
    }

    public function performActionInApiInitializeScope(callable $action): void
    {
        $this->mockApiPaymentInitializeResponse();
        $action();
        $this->mocker->unmockAll();
    }

    public function performActionInApiSuccessfulScope(callable $action): void
    {
        $this->mockApiSuccessfulPaymentResponse();
        $action();
        $this->mocker->unmockAll();
    }

    private function mockApiSuccessfulPaymentResponse(): void
    {
        $mockedResponse = $this->responseLoader->getMockedResponse('Paypal/paypal_api_successful_payment.json');
        $firstGetExpressCheckoutDetailsStream = $this->mockStream($mockedResponse['firstGetExpressCheckoutDetails']);
        $firstGetExpressCheckoutDetailsResponse = $this->mockHttpResponse(200, $firstGetExpressCheckoutDetailsStream);

        $doExpressCheckoutPaymentStream = $this->mockStream($mockedResponse['doExpressCheckoutPayment']);
        $doExpressCheckoutPaymentResponse = $this->mockHttpResponse(200, $doExpressCheckoutPaymentStream);

        $secondGetExpressCheckoutDetailsStream = $this->mockStream($mockedResponse['secondGetExpressCheckoutDetails']);
        $secondGetExpressCheckoutDetailsResponse = $this->mockHttpResponse(200, $secondGetExpressCheckoutDetailsStream);

        $getTransactionDetailsStream = $this->mockStream($mockedResponse['getTransactionDetails']);
        $getTransactionDetailsResponse = $this->mockHttpResponse(200, $getTransactionDetailsStream);

        $this->mocker->mockService('sylius.payum.http_client', HttpClientInterface::class)
            ->expects('send')
            ->times(4)
            ->andReturns($firstGetExpressCheckoutDetailsResponse, $doExpressCheckoutPaymentResponse, $secondGetExpressCheckoutDetailsResponse, $getTransactionDetailsResponse)
        ;
    }

    private function mockApiPaymentInitializeResponse(): void
    {
        $mockedResponse = $this->responseLoader->getMockedResponse('Paypal/paypal_api_initialize_payment.json');
        $setExpressCheckoutStream = $this->mockStream($mockedResponse['setExpressCheckout']);
        $setExpressCheckoutResponse = $this->mockHttpResponse(200, $setExpressCheckoutStream);

        $getExpressCheckoutDetailsStream = $this->mockStream($mockedResponse['getExpressCheckoutDetails']);
        $getExpressCheckoutDetailsResponse = $this->mockHttpResponse(200, $getExpressCheckoutDetailsStream);

        $this->mocker->mockService('sylius.payum.http_client', HttpClientInterface::class)
            ->expects('send')
            ->twice()
            ->andReturns($setExpressCheckoutResponse, $getExpressCheckoutDetailsResponse)
        ;
    }

    private function mockStream(string $content): MockInterface
    {
        $mockedStream = $this->mocker->mockCollaborator(StreamInterface::class);
        $mockedStream->expects('getContents')->andReturns($content);
        $mockedStream->expects('close')->andReturns();

        return $mockedStream;
    }

    private function mockHttpResponse(int $statusCode, MockInterface $streamMock): MockInterface
    {
        $mockedHttpResponse = $this->mocker->mockCollaborator(ResponseInterface::class);
        $mockedHttpResponse->expects('getStatusCode')->andReturns($statusCode);
        $mockedHttpResponse->expects('getBody')->andReturns($streamMock);

        return $mockedHttpResponse;
    }
}
