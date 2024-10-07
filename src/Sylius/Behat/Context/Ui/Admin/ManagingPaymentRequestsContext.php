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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Payment\PaymentRequest\IndexPageInterface;
use Sylius\Behat\Page\Admin\Payment\PaymentRequest\ShowPageInterface;
use Sylius\Behat\Service\SharedSecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingPaymentRequestsContext implements Context
{
    public function __construct(
        private IndexPageInterface $indexPage,
        private ShowPageInterface $showPage,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private SharedStorageInterface $sharedStorage,
        private SharedSecurityServiceInterface $sharedSecurityService,
    ) {
    }

    /**
     * @When I browse payment requests of an order :order
     */
    public function iBrowsePaymentRequestsOfACustomer(OrderInterface $order): void
    {
        $this->indexPage->open(['paymentId' => $order->getLastPayment()->getId()]);
    }

    /**
     * @When I view details of the payment request for the :order order
     */
    public function iViewDetailsOfThePaymentRequestForTheOrder(OrderInterface $order): void
    {
        $payment = $order->getLastPayment();
        $paymentRequest = $this->paymentRequestRepository->findOneBy(['payment' => $payment]);

        $this->showPage->open([
            'hash' => $paymentRequest->getHash(),
            'paymentId' => $payment->getId(),
        ]);
    }

    /**
     * @When I filter by the :action action
     */
    public function iFilterByTheAction(string $action): void
    {
        $this->indexPage->chooseActionToFilter($action);
        $this->indexPage->filter();
    }

    /**
     * @When I filter by the :paymentMethod payment method
     */
    public function iFilterByThePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->indexPage->choosePaymentMethodToFilter($paymentMethod->getName());
        $this->indexPage->filter();
    }

    /**
     * @When I filter by the :state state
     */
    public function iFilterByTheState(string $state): void
    {
        $this->indexPage->chooseStateToFilter($state);
        $this->indexPage->filter();
    }

    /**
     * @Then /^there should be (\d+) payment requests? on the list$/
     */
    public function thereShouldBeProductVariantsOnTheList(int $count): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then it should be the payment request with action :action
     */
    public function itShouldBeThePaymentRequestWithAction(string $action): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['action' => $action]));
    }

    /**
     * @Then it should be the payment request with payment method :paymentMethod
     */
    public function itShouldBeThePaymentRequestWithPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['method' => $paymentMethod->getName()]));
    }

    /**
     * @Then its :field should be :value
     */
    public function itsFieldShouldBe(string $field, string $value): void
    {
        Assert::same($this->showPage->getFieldText($field), $value);
    }

    /**
     * @Then its payload should has empty value
     */
    public function itsPayloadShouldHasEmptyValue(): void
    {
        Assert::same($this->showPage->getFieldText('payload'), 'null');
    }

    /**
     * @Then its response data should has empty value
     */
    public function itsResponseDataShouldBe(): void
    {
        Assert::same($this->showPage->getFieldText('response_data'), json_encode([]));
    }

    /**
     * @Then the administrator should see the payment request with action :action for :method payment method and state :state
     */
    public function administratorShouldSeeThePaymentRequestWithActionAndState(string $action, string $paymentMethod, string $state): void
    {
        $adminUser = $this->sharedStorage->get('administrator');

        $this->sharedSecurityService->performActionAsAdminUser($adminUser, function () {
            $this->iBrowsePaymentRequestsOfACustomer($this->sharedStorage->get('order'));
        });

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'action' => $action,
            'state' => $state,
            'method' => $paymentMethod,
        ]));
    }
}
