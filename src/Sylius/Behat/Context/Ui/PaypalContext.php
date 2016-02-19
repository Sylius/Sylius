<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\PaypalMockedApiResponsesInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaypalContext implements Context
{
    /**
     * @var PaypalExpressCheckoutPageInterface
     */
    private $paypalExpressCheckoutPage;

    /**
     * @var PaypalMockedApiResponsesInterface
     */
    private $paypalMockedApiResponses;

    /**
     * @param PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
     * @param PaypalMockedApiResponsesInterface $paypalMockedApiResponses
     */
    public function __construct(
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        PaypalMockedApiResponsesInterface $paypalMockedApiResponses
    ) {
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->paypalMockedApiResponses = $paypalMockedApiResponses;
    }

    /**
     * @Then I should be redirected to PayPal Express Checkout page
     */
    public function iShouldBeRedirectedToPaypalExpressCheckoutPage()
    {
        expect($this->paypalExpressCheckoutPage->isOpen())->toBe(true);
    }

    /**
     * @When I sign in to PayPal and pay successfully
     */
    public function iSignInToPaypalAndPaySuccessfully()
    {
        $this->paypalMockedApiResponses->mockApiSuccessfulPaymentResponse();
        $this->paypalExpressCheckoutPage->pay();
    }

    /**
     * @When I cancel my PayPal payment
     */
    public function iCancelMyPaypalPayment()
    {
        $this->paypalExpressCheckoutPage->cancel();
    }
}
