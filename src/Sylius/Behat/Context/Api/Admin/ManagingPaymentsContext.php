<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;

final class ManagingPaymentsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Given I am browsing payments
     * @When I browse payments
     */
    public function iAmBrowsingPayments(): void
    {
        $this->client->index('payments');
    }

    /**
     * @Then I should see a single payment in the list
     * @Then I should see :count payments in the list
     */
    public function iShouldSeePaymentsInTheList(int $count = 1): void
    {
        Assert::same($this->client->countCollectionItems(), $count);
    }

    /**
     * @Then the payment of the :orderNumber order should be :paymentState for :customer
     */
    public function thePaymentOfTheOrderShouldBeFor(
        string $orderNumber,
        string $paymentState,
        CustomerInterface $customer
    ): void {
        foreach ($this->client->getCollectionItemsWithValue('state', StringInflector::nameToLowercaseCode($paymentState)) as $payment) {
            $this->client->showByIri($payment['order']);
            if ($this->client->responseHasValue('number', $orderNumber)) {
                $this->client->showByIri($this->client->getResponseValue('customer'));
                if ($this->client->responseHasValue('email', $customer->getEmail())) {
                    return;
                }
            }
        }

        throw new \InvalidArgumentException('There is no payment with given data.');
    }

    /**
     * @Then /^I should see payment for (the "[^"]+" order) as (\d+)(?:|st|nd|rd|th) in the list$/
     */
    public function iShouldSeePaymentForTheOrderInTheList(string $orderNumber, int $position): void
    {
        Assert::true(
            $this->client->hasItemOnPositionWithValue($position - 1, 'order', sprintf('/new-api/orders/%s', $orderNumber)),
        );
    }
}
