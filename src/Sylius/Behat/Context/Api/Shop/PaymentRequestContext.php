<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;

final class PaymentRequestContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private RequestFactoryInterface $requestFactory,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    /**
     * @When I try to pay for my order
     */
    public function iTryToPayForMyOrder(array $payload = []): void
    {
        $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));

        $payments = $this->responseChecker->getValue($this->client->getLastResponse(), 'payments');
        $payment = end($payments);
        $this->postPaymentRequest($payment, $payload);

        $uri = $this->responseChecker->getValue($this->client->getLastResponse(), '@id');
        $this->sharedStorage->set('payment_request_uri', $uri);
    }

    /**
     * @When I try to update my payment request
     */
    public function iTryToUpdateMyPaymentRequest(array $payload = []): void
    {
        $this->putPaymentRequest($this->sharedStorage->get('payment_request_uri'), $payload);
    }

    private function postPaymentRequest(array $payment, array $payload): void
    {
        $request = $this->requestFactory->create(
            'shop',
            Resources::PAYMENT_REQUESTS,
            'Authorization',
            $this->client->getToken(),
        );

        $request->setContent([
            'paymentId' => $payment['id'],
            'paymentMethodCode' => $payment['method'],
            'payload' => $payload,
        ]);

        $this->client->executeCustomRequest($request);
    }

    public function putPaymentRequest(string $paymentRequestUri, array $payload = []): void
    {
        $request = $this->requestFactory->custom(
            $paymentRequestUri,
            HttpRequest::METHOD_PUT,
            [],
            $this->client->getToken(),
        );

        $request->setContent([
            'payload' => $payload,
        ]);

        $this->client->executeCustomRequest($request);
    }
}
