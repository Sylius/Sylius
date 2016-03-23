<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Mocker;

use Guzzle\Http\Message\Response;
use Guzzle\Stream\Stream;
use Mockery\Mock;
use Payum\Core\Bridge\Guzzle\HttpClient;
use Sylius\Behat\Service\ResponseLoaderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalApiMocker
{
    /**
     * @var MockerInterface
     */
    private $mocker;

    /**
     * @var ResponseLoaderInterface
     */
    private $responseLoader;

    /**
     * @param MockerInterface $mocker
     * @param ResponseLoaderInterface $responseLoader
     */
    public function __construct(MockerInterface $mocker, ResponseLoaderInterface $responseLoader)
    {
        $this->mocker = $mocker;
        $this->responseLoader = $responseLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function mockApiSuccessfulPaymentResponse()
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

        $this->mocker->mockService('payum.http_client', HttpClient::class)
            ->shouldReceive('send')
            ->times(4)
            ->andReturn($firstGetExpressCheckoutDetailsResponse, $doExpressCheckoutPaymentResponse, $secondGetExpressCheckoutDetailsResponse, $getTransactionDetailsResponse)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function mockApiPaymentInitializeResponse()
    {
        $mockedResponse = $this->responseLoader->getMockedResponse('Paypal/paypal_api_initialize_payment.json');
        $setExpressCheckoutStream = $this->mockStream($mockedResponse['setExpressCheckout']);
        $setExpressCheckoutResponse = $this->mockHttpResponse(200, $setExpressCheckoutStream);

        $getExpressCheckoutDetailsStream = $this->mockStream($mockedResponse['getExpressCheckoutDetails']);
        $getExpressCheckoutDetailsResponse = $this->mockHttpResponse(200, $getExpressCheckoutDetailsStream);

        $this->mocker->mockService('payum.http_client', HttpClient::class)
            ->shouldReceive('send')
            ->twice()
            ->andReturn($setExpressCheckoutResponse, $getExpressCheckoutDetailsResponse)
        ;
    }

    /**
     * @param string $content
     *
     * @return Mock
     */
    private function mockStream($content)
    {
        $mockedStream = $this->mocker->mockCollaborator(Stream::class);
        $mockedStream->shouldReceive('getContents')->once()->andReturn($content);
        $mockedStream->shouldReceive('close')->once()->andReturn();

        return $mockedStream;
    }

    /**
     * @param int $statusCode
     * @param mixed $streamMock
     *
     * @return Mock
     */
    private function mockHttpResponse($statusCode, $streamMock)
    {
        $mockedHttpResponse = $this->mocker->mockCollaborator(Response::class);
        $mockedHttpResponse->shouldReceive('getStatusCode')->once()->andReturn($statusCode);
        $mockedHttpResponse->shouldReceive('getBody')->once()->andReturn($streamMock);

        return $mockedHttpResponse;
    }
}
