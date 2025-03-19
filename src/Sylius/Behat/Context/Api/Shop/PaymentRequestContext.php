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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final readonly class PaymentRequestContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private RequestFactoryInterface $requestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
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

    /**
     * @Then a payment request with action :action for payment method :paymentMethod should have state :state
     */
    public function aPaymentRequestWithActionForPaymentMethodShouldHaveState(string $action, PaymentMethodInterface $paymentMethod, string $state): void
    {
        $request = $this->getRequestForPaymentRequestWithAction($action);
        Assert::notNull($request, sprintf('Payment request with action %s not found', $action));

        $this->client->executeCustomRequest($request);
        $response = $this->client->getLastResponse();

        Assert::same($this->responseChecker->getValue($response, 'action'), $action, sprintf('Payment request should have action %s', $action));
        Assert::contains($this->responseChecker->getValue($response, 'method'), $paymentMethod->getCode(), sprintf('Payment request should have payment method %s', $paymentMethod->getCode()));
        Assert::same($this->responseChecker->getValue($response, 'state'), $state, sprintf('Payment request should have state %s', $state));
    }

    private function postPaymentRequest(array $payment, array $payload): void
    {
        $request = $this->requestFactory->create(
            'shop',
            sprintf('orders/%s/payment-requests', $this->sharedStorage->get('cart_token')),
            'Authorization',
            $this->client->getToken(),
        );

        $request->setContent([
            'paymentId' => $payment['id'],
            'paymentMethodCode' => $payment['method'],
            'action' => PaymentRequestInterface::ACTION_CAPTURE,
            'payload' => $payload,
        ]);

        $this->client->executeCustomRequest($request);
    }

    private function putPaymentRequest(string $paymentRequestUri, array $payload = []): void
    {
        $request = $this->requestFactory->custom(
            $paymentRequestUri,
            HttpRequest::METHOD_PUT,
            [],
            $this->client->getToken(),
        );

        $request->setContent(['payload' => $payload]);

        $this->client->executeCustomRequest($request);
    }

    private function getRequestForPaymentRequestWithAction(string $action): ?Request
    {
        $orderToken = $this->sharedStorage->get('cart_token');
        $order = $this->client->show(Resources::ORDERS, $orderToken);
        $payments = $this->responseChecker->getValue($order, 'payments');
        $paymentId = end($payments)['id'];
        $paymentRequest = $this->paymentRequestRepository->findOneBy(['payment' => $paymentId, 'action' => $action]);

        return $paymentRequest ? $this->requestFactory->custom('/api/v2/shop/payment-requests/' . $paymentRequest->getHash(), HttpRequest::METHOD_GET, [], $this->client->getToken()) : null;
    }
}
