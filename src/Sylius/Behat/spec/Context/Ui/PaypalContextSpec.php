<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Behat\Context\Ui;
 
use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\PaypalApiMocker;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalContextSpec extends ObjectBehavior
{
    function let(PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage, PaypalApiMocker $paypalApiMocker)
    {
        $this->beConstructedWith($paypalExpressCheckoutPage, $paypalApiMocker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\PaypalContext');
    }

    function it_is_a_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_redirection_from_paypal_express_checkout($paypalExpressCheckoutPage)
    {
        $paypalExpressCheckoutPage->isOpen()->willReturn(true);

        $this->iShouldBeRedirectedToPaypalExpressCheckoutPage();
    }

    function it_logs_in_and_pay_on_paypal_page($paypalExpressCheckoutPage, $paypalApiMocker)
    {
        $paypalExpressCheckoutPage->pay()->shouldBeCalled();
        $paypalApiMocker->mockApiSuccessfulPaymentResponse()->shouldBeCalled();

        $this->iSignInToPaypalAndPaySuccessfully();
    }

    function it_cancels_payment($paypalExpressCheckoutPage)
    {
        $paypalExpressCheckoutPage->cancel()->shouldBeCalled();

        $this->iCancelMyPaypalPayment();
    }
}
