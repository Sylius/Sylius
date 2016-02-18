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
use Sylius\Behat\Page\External\PaypalExpressCheckoutPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalContext implements Context
{
    /**
     * @var PaypalExpressCheckoutPage
     */
    private $paypalExpressCheckoutPage;

    /**
     * @var string
     */
    private $paypalAccountName;

    /**
     * @var string
     */
    private $paypalAccountPassword;

    /**
     * @param PaypalExpressCheckoutPage $paypalExpressCheckoutPage
     * @param string $paypalAccountName
     * @param string $paypalAccountPassword
     */
    public function __construct(PaypalExpressCheckoutPage $paypalExpressCheckoutPage, $paypalAccountName, $paypalAccountPassword)
    {
        $this->paypalExpressCheckoutPage = $paypalExpressCheckoutPage;
        $this->paypalAccountName = $paypalAccountName;
        $this->paypalAccountPassword = $paypalAccountPassword;
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
        $this->paypalExpressCheckoutPage->logIn($this->paypalAccountName, $this->paypalAccountPassword);
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
