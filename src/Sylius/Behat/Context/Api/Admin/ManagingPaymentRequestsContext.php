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
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\IriConverterInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final readonly class ManagingPaymentRequestsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private RequestFactoryInterface $requestFactory,
        private SharedSecurityServiceInterface $sharedSecurityService,
        private SharedStorageInterface $sharedStorage,
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
            (string) $order->getLastPayment()->getId(),
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
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->getLastResponse(),
                'method',
                $this->iriConverter->getIriFromResourceInSection($paymentMethod, 'admin'),
            ),
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

    /**
     * @Then the administrator should see the payment request with action :action for :paymentMethod payment method and state :state
     */
    public function administratorShouldSeeThePaymentRequestWithActionAndState(string $action, PaymentMethodInterface $paymentMethod, string $state): void
    {
        $adminUser = $this->sharedStorage->get('administrator');

        /** @var OrderInterface $order */
        $this->sharedSecurityService->performActionAsAdminUser($adminUser, function () {
            $order = $this->sharedStorage->get('order');

            $request = $this->requestFactory->custom('/api/v2/admin/payments/' . $order->getLastPayment()->getId() . '/payment-requests', HTTPRequest::METHOD_GET, [], $this->client->getToken());
            $this->client->executeCustomRequest($request);
        });

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'action', $action),
            sprintf('Payment request should have action %s', $action),
        );
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'method', $this->iriConverter->getIriFromResourceInSection($paymentMethod, 'admin')),
            sprintf('Payment request should have payment method %s', $paymentMethod->getCode()),
        );
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'state', $state),
            sprintf('Payment request should have state %s', $state),
        );
    }
}
