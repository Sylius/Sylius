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
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\Mocker\PaypalApiMocker;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\Request as HTTPRequest;
use Webmozart\Assert\Assert;

final class PaypalContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private RequestFactoryInterface $requestFactory,
        private PaypalApiMocker $paypalApiMocker,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    /**
     * @When /^I confirm my order with paypal payment$/
     * @Given /^I have confirmed my order with paypal payment$/
     */
    public function iConfirmMyOrderWithPaypalPayment(): void
    {
        $this->completeOrder();

        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $payment = $this->responseChecker->getValue($this->client->getLastResponse(), 'payments')[0];
            $this->postPaymentRequest($payment);

            $uri = $this->responseChecker->getValue($this->client->getLastResponse(), '@id');
            $this->sharedStorage->set('payment_request_uri', $uri);
        });
    }

    /**
     * @When I sign in to PayPal and authorize successfully
     */
    public function iSignInToPaypalAndAuthorizeSuccessfully(): void
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function () {
            $uri = $this->sharedStorage->get('payment_request_uri');
            $request = $this->requestFactory->custom(
                $uri,
                HttpRequest::METHOD_PUT,
                [],
                $this->client->getToken(),
            );

            $request->setContent([
                'requestPayload' => [
                    'target_path' => 'https://myshop.tld/target-path',
                    'after_path' => 'https://myshop.tld/after-path',
                    'http_request' => [
                        'query' => [
                            'token'=>'EC-2d9EV13959UR209410U',
                            'PayerID'=>'UX8WBNYWGBVMG',
                        ]
                    ],
                ],
            ]);

            $this->client->executeCustomRequest($request);
        });
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully(): void
    {
        $this->paypalApiMocker->performActionInApiSuccessfulScope(function () {
            $this->paypalExpressCheckoutPage->pay();
        });
    }

    /**
     * @Given /^I have cancelled (?:|my )PayPal payment$/
     * @When /^I cancel (?:|my )PayPal payment$/
     */
    public function iCancelMyPaypalPayment(): void
    {
        $this->paypalExpressCheckoutPage->cancel();
    }

    /**
     * @When /^I try to pay(?:| again)$/
     */
    public function iTryToPayAgain(): void
    {
        $this->paypalApiMocker->performActionInApiInitializeScope(function () {
            $this->orderDetails->pay();
        });
    }

    /**
     * @Then I should be notified that my payment has been cancelled
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCancelled(): void
    {
        $this->assertNotification('Payment has been cancelled.');
    }

    /**
     * @Then I should be notified that my payment has been completed
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCompleted(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            sprintf(
                'Payment request could not be edited: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    private function completeOrder(): void
    {
        $request = $this->requestFactory->customItemAction(
            'shop',
            Resources::ORDERS,
            $this->sharedStorage->get('cart_token'),
            HTTPRequest::METHOD_PATCH,
            'complete',
        );

        $this->client->executeCustomRequest($request);
    }

    private function postPaymentRequest(array $payment): void
    {
        $request = $this->requestFactory->create(
            'shop',
            Resources::PAYMENT_REQUESTS,
            'Authorization',
            $this->client->getToken(),
        );

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([]);
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $authorize = $gatewayConfig->getConfig()['use_authorize'] ?? false;

        $request->setContent([
            'paymentId' => $payment['@id'],
            'paymentMethodCode' => $payment['method'],
            'type' => $authorize
                ? PaymentRequestInterface::DATA_TYPE_AUTHORIZE
                : PaymentRequestInterface::DATA_TYPE_CAPTURE
            ,
            'requestPayload' => [
                'target_path' => 'https://myshop.tld/target-path',
                'after_path' => 'https://myshop.tld/after-path',
            ],
        ]);

        $this->client->executeCustomRequest($request);
    }
}
