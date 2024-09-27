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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\IriConverterInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final readonly class ManagingPaymentRequestsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    /**
     * @When I browse payment requests of an order :order
     */
    public function iBrowseOrdersOfACustomer(OrderInterface $order): void
    {
        $this->client->subResourceIndex(
            Resources::PAYMENTS,
            Resources::PAYMENT_REQUESTS,
            (string) $order->getLastPayment()->getId()
        );
    }

    /**
     * @When I view details of the payment request for the :order order
     */
    public function iViewDetailsOfThePaymentRequestForTheOrder(OrderInterface $order): void
    {
        $paymentRequest = $this->paymentRequestRepository->findOneBy(['payment' => $order->getLastPayment()]);

        $this->client->show(Resources::PAYMENT_REQUESTS, (string) $paymentRequest->getHash());
    }

    /**
     * @When I filter by the :action action
     */
    public function iFilterByTheAction(string $action): void
    {
        $this->client->addFilter('action', $action);
        $this->client->filter();
    }

    /**
     * @When I filter by the :paymentMethod payment method
     */
    public function iFilterByThePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->client->addFilter('method.code', $paymentMethod->getCode());
        $this->client->filter();
    }

    /**
     * @When I filter by the :state state
     */
    public function iFilterByTheState(string $state): void
    {
        $this->client->addFilter('state', $state);
        $this->client->filter();
    }

    /**
     * @Then /^there should be (\d+) payment requests? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then it should be the payment request with action :action
     */
    public function itShouldBeThePaymentRequestWithAction(string $action): void
    {
        Assert::true($this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'action', $action));
    }

    /**
     * @Then it should be the payment request with payment method :paymentMethod
     */
    public function itShouldBeThePaymentRequestWithPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'method',
                $this->iriConverter->getIriFromResourceInSection($paymentMethod, 'admin'),
            ),
        );
    }

    /**
     * @Then its method should be :paymentMethod
     */
    public function itsMethodShouldBe(PaymentMethodInterface $paymentMethod): void
    {
        Assert::true($this->responseChecker->hasValue(
            $this->client->getLastResponse(),
            'method',
            $this->iriConverter->getIriFromResourceInSection($paymentMethod, 'admin'))
        );
    }

    /**
     * @Then /^its (action|state) should be "([^"]+)"$/
     */
    public function itsActionStateShouldBe(string $field, string $value): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), $field, strtolower($value)));
    }

    /**
     * @Then its payload should has empty value
     */
    public function itsPayloadShouldHasEmptyValue(): void
    {
        Assert::isEmpty($this->responseChecker->getValue($this->client->getLastResponse(), 'payload'));
    }

    /**
     * @Then its response data should has empty value
     */
    public function itsResponseDataShouldHasEmptyValue(): void
    {
        Assert::isEmpty($this->responseChecker->getValue($this->client->getLastResponse(), 'responseData'));
    }

}
