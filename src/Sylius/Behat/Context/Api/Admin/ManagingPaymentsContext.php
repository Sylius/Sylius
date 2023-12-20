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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private IriConverterInterface $iriConverter,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @Given I am browsing payments
     * @When I browse payments
     */
    public function iAmBrowsingPayments(): void
    {
        $this->client->index(Resources::PAYMENTS);
    }

    /**
     * @When I go to the details of the first payment's order
     */
    public function iGoToTheDetailsOfTheFirstPaymentSOrder(): void
    {
        $firstPayment = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        /** @var OrderInterface $order */
        $order = $this->iriConverter->getResourceFromIri($firstPayment['order']);

        $this->client->customItemAction(Resources::ORDERS, $order->getTokenValue(), HttpRequest::METHOD_GET, 'payments');
    }

    /**
     * @Then I should see the details of order :order
     */
    public function iShouldSeeOrderWithDetails(OrderInterface $order): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'order',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
            ),
            sprintf('Order with number %s does not exist', $order->getNumber()),
        );
    }

    /**
     * @When I complete the payment of order :order
     */
    public function iCompleteThePaymentOfOrder(OrderInterface $order): void
    {
        $payment = $order->getLastPayment();
        Assert::notNull($payment);

        $this->client->applyTransition(
            Resources::PAYMENTS,
            (string) $payment->getId(),
            PaymentTransitions::TRANSITION_COMPLETE,
        );
    }

    /**
     * @When I choose :state as a payment state
     */
    public function iChooseAsAPaymentState(string $state): void
    {
        $this->client->addFilter('state', $state);
    }

    /**
     * @When I choose :channel as a channel filter
     */
    public function iChooseChannelAsAChannelFilter(ChannelInterface $channel): void
    {
        $this->client->addFilter('order.channel.code', $channel->getCode());
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();
    }

    /**
     * @Then I should see a single payment in the list
     * @Then I should see :count payments in the list
     */
    public function iShouldSeePaymentsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the payment of the :orderNumber order should be :paymentState for :customer
     */
    public function thePaymentOfTheOrderShouldBeFor(
        string $orderNumber,
        string $paymentState,
        CustomerInterface $customer,
    ): void {
        $payments = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'state',
            StringInflector::nameToLowercaseCode($paymentState),
        );

        foreach ($payments as $payment) {
            $this->client->showByIri($payment['order']);
            $orderResponse = $this->client->getLastResponse();

            if (!$this->responseChecker->hasValue($orderResponse, 'number', $orderNumber)) {
                continue;
            }

            $this->client->showByIri($this->responseChecker->getValue($orderResponse, 'customer'));
            $customerResponse = $this->client->getLastResponse();

            if ($this->responseChecker->hasValue($customerResponse, 'email', $customer->getEmail())) {
                return;
            }
        }

        throw new \InvalidArgumentException('There is no payment with given data.');
    }

    /**
     * @Then /^I should see payment for the ("[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeePaymentForTheOrderInTheList(OrderInterface $order, int $position): void
    {
        Assert::true($this->responseChecker->hasItemOnPositionWithValue(
            $this->client->getLastResponse(),
            $position - 1,
            'order',
            sprintf('%s/admin/orders/%s', $this->apiUrlPrefix, $order->getTokenValue()),
        ));
    }

    /**
     * @Then I should be notified that the payment has been completed
     */
    public function iShouldBeNotifiedThatThePaymentHasBeenCompleted(): void
    {
        Assert::true($this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()), 'Resource could not be completed');
    }

    /**
     * @Then I should see the payment of order :order as :paymentState
     */
    public function iShouldSeeThePaymentOfOrderAs(OrderInterface $order, string $paymentState): void
    {
        $payment = $order->getLastPayment();
        Assert::notNull($payment);

        Assert::true($this->responseChecker->hasValue(
            $this->client->show(Resources::PAYMENTS, (string) $payment->getId()),
            'state',
            StringInflector::nameToLowercaseCode($paymentState),
        ));
    }

    /**
     * @Then I should see (also) the payment of the :order order
     */
    public function iShouldSeeThePaymentOfTheOrder(OrderInterface $order): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->getLastResponse(),
            'order',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
        ));
    }

    /**
     * @Then I should not see the payment of the :order order
     */
    public function iShouldNotSeeThePaymentOfTheOrder(OrderInterface $order): void
    {
        Assert::false($this->responseChecker->hasItemWithValue(
            $this->client->getLastResponse(),
            'order',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($order, 'admin'),
        ));
    }
}
