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

namespace Sylius\Behat\Context\Api\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Payment\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class CheckoutOrderDetailsContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
    ) {
    }

    /**
     * @When /^I want to browse order details for (this order)$/
     */
    public function iWantToBrowseOrderDetailsForThisOrder(OrderInterface $order): void
    {
        $this->sharedStorage->set('cart_token', $order->getTokenValue());
        $this->client->show(Resources::ORDERS, $order->getTokenValue());
    }

    /**
     * @Then I should be able to pay (again)
     */
    public function iShouldBeAbleToPay(): void
    {
        $state = $this->getLatestPaymentState();
        Assert::eq($state, PaymentInterface::STATE_NEW);
    }

    /**
     * @Then I should not be able to pay (again)
     */
    public function iShouldNotBeAbleToPay(): void
    {
        $state = $this->getLatestPaymentState();
        Assert::notEq($state, PaymentInterface::STATE_NEW);
    }

    private function getLatestPaymentState(): ?string
    {
        $response = $this->client->show(Resources::ORDERS, $this->sharedStorage->get('cart_token'));
        Assert::same($this->client->getLastResponse()->getStatusCode(), 200);

        // If the payment is canceled we won't be able to retrieve it because only new one are retrievable
        if (OrderPaymentStates::STATE_CANCELLED === $this->responseChecker->getValue($response, 'paymentState')) {
            return PaymentInterface::STATE_CANCELLED;
        }

        $payments = $this->responseChecker->getValue($response, 'payments');
        $payment = end($payments);

        $paymentId = $payment['id'];
        $response = $this->client->show(Resources::PAYMENTS, (string) $paymentId);

        return $this->responseChecker->getValue($response, 'state');
    }
}
