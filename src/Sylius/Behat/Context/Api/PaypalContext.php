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

namespace Sylius\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Shop\Checkout\CheckoutCompleteContext;
use Sylius\Behat\Context\Api\Shop\PaymentRequestContext;
use Sylius\Behat\Service\Mocker\PaypalApiMocker;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class PaypalContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private PaypalApiMocker $paypalApiMocker,
        private CheckoutCompleteContext $checkoutCompleteContext,
        private PaymentRequestContext $paymentRequestContext,
    ) {
    }

    /**
     * @When /^I confirm my order with paypal payment$/
     * @Given /^I have confirmed my order with paypal payment$/
     */
    public function iConfirmMyOrderWithPaypalPayment(): void
    {
        $this->checkoutCompleteContext->iConfirmMyOrder();

        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $this->paymentRequestContext->iTryToPayForMyOrder([
                'target_path' => 'https://myshop.tld/target-path',
                'after_path' => 'https://myshop.tld/after-path',
            ]);
        });
    }

    /**
     * @When I sign in to PayPal and authorize successfully
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndAuthorizeOrPaySuccessfully(): void
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function () {
            $this->paymentRequestContext->iTryToUpdateMyPaymentRequest([
                'target_path' => 'https://myshop.tld/target-path',
                'after_path' => 'https://myshop.tld/after-path',
                'http_request' => [
                    'query' => [
                        'token' => 'EC-2d9EV13959UR209410U',
                        'PayerID' => 'UX8WBNYWGBVMG',
                    ],
                ],
            ]);
        });
    }

    /**
     * @Given /^I have cancelled (?:|my )PayPal payment$/
     * @When /^I cancel (?:|my )PayPal payment$/
     */
    public function iCancelMyPaypalPayment(): void
    {
        $this->paymentRequestContext->iTryToUpdateMyPaymentRequest([
            'target_path' => 'https://myshop.tld/target-path',
            'after_path' => 'https://myshop.tld/after-path',
            'http_request' => [
                'query' => [
                    'token' => 'EC-2d9EV13959UR209410U',
                    'cancelled' => 1,
                ],
            ],
        ]);
    }

    /**
     * @When /^I try to pay(?:| again)$/
     */
    public function iTryToPayAgain(): void
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $this->paymentRequestContext->iTryToPayForMyOrder([
                'target_path' => 'https://myshop.tld/target-path',
                'after_path' => 'https://myshop.tld/after-path',
            ]);
        });
    }

    /**
     * @Then I should be notified that my payment has been cancelled
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCancelled(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            sprintf(
                'Payment request could not be updated: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    /**
     * @Then I should be notified that my payment has been completed
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCompleted(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            sprintf(
                'Payment request could not be updated: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }
}
