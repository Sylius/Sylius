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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Page\Shop\PaymentRequest\PaymentMethodNotifyPageInterface;
use Sylius\Behat\Page\Shop\PaymentRequest\PaymentRequestNotifyPage;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class PaymentRequestContext implements Context
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentMethodNotifyPageInterface $paymentMethodNotifyPage,
        private PaymentRequestNotifyPage $paymentRequestNotifyPage,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @When I call the payment method notify page with the code :paymentMethod
     */
    public function iCallThePaymentMethodNotifyPageWithTheCode(string $paymentMethodCode): void
    {
        $this->paymentMethodNotifyPage->openWithClient(
            'GET',
            ['code' => $paymentMethodCode],
        );
    }

    /**
     * @When /^I call the payment request notify page for this payment request$/
     */
    public function iCallThePaymentRequestNotifyPageForThisPaymentRequest(): void
    {
        /** @var PaymentRequestInterface[] $paymentRequests */
        $paymentRequests = $this->paymentRequestRepository->findBy(
            ['action' => PaymentRequestInterface::ACTION_NOTIFY],
            ['createdAt' => 'ASC'],
            1,
        );

        $paymentRequest = $paymentRequests[0];

        $this->paymentRequestNotifyPage->openWithClient(
            'GET',
            ['hash' => $paymentRequest->getHash()],
        );
    }

    /**
     * @Then a payment request with action :action for payment method :paymentMethod should have state :state
     */
    public function aPaymentRequestWithActionForPaymentMethodShouldHaveState(string $action, PaymentMethodInterface $paymentMethod, string $state): void
    {
        $this->objectManager->clear(); // avoiding doctrine cache

        /** @var PaymentRequestInterface[] $paymentRequests */
        $paymentRequests = $this->paymentRequestRepository->findBy(
            [
                'action' => $action,
            ],
            ['createdAt' => 'ASC'],
            1,
        );

        Assert::count($paymentRequests, 1);
        $paymentRequest = $paymentRequests[0];
        Assert::notNull($paymentRequest);
        Assert::eq($paymentRequest->getMethod()->getId(), $paymentMethod->getId());
        Assert::eq($paymentRequest->getState(), $state);
    }

    /**
     * @Then /^no payment request with "([^"]*)" action should exists$/
     */
    public function noPaymentRequestWithActionShouldExists(string $action, ?string $state = null): void
    {
        /** @var PaymentRequestInterface[] $paymentRequests */
        $paymentRequests = $this->paymentRequestRepository->findBy(
            ['action' => $action],
            ['createdAt' => 'ASC'],
            1,
        );

        Assert::isEmpty($paymentRequests);
    }

    /**
     * @Given /^the response content should be empty$/
     */
    public function theResponseContentShouldBeEmpty(): void
    {
        $response = $this->paymentMethodNotifyPage->getClient()->getInternalResponse();

        Assert::isEmpty($response->getContent());
    }

    /**
     * @Given /^the response status code should be (\d+)$/
     */
    public function theResponseStatusCodeShouldBe(int $statusCode): void
    {
        $response = $this->paymentMethodNotifyPage->getClient()->getInternalResponse();

        Assert::eq($response->getStatusCode(), $statusCode);
    }
}
