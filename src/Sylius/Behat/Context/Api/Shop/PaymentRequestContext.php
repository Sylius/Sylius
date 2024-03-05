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
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final class PaymentRequestContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private RequestFactoryInterface $requestFactory,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then the payment request for payment method :paymentMethod should be cancelled
     */
    public function myPaymentRequestShouldBeCancelled(PaymentMethodInterface $paymentMethod): void
    {
        $paymentRequestUri = $this->sharedStorage->get('payment_request_uri');

        $request = $this->requestFactory->custom(
            $paymentRequestUri,
            HttpRequest::METHOD_GET,
            [],
            $this->client->getToken(),
        );

        $response = $this->client->executeCustomRequest($request);

        Assert::same($this->responseChecker->getValue($response, 'state'), PaymentRequestInterface::STATE_CANCELLED, sprintf('Payment request should be cancelled'));
        Assert::true(str_contains($this->responseChecker->getValue($response, 'method'), $paymentMethod->getCode()), sprintf('Payment request should be for %s payment method', $paymentMethod->getCode()));
    }
}
