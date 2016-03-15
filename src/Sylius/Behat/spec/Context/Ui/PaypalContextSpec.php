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
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Checkout\CheckoutFinalizeStepInterface;
use Sylius\Behat\Page\External\PaypalExpressCheckoutPageInterface;
use Sylius\Behat\Page\Order\OrderPaymentsPageInterface;
use Sylius\Behat\PaypalApiMocker;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaypalContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        OrderPaymentsPageInterface $orderPaymentsPage,
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        CheckoutFinalizeStepInterface $checkoutFinalizeStep,
        PaypalApiMocker $paypalApiMocker,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $orderPaymentsPage,
            $paypalExpressCheckoutPage,
            $checkoutFinalizeStep,
            $paypalApiMocker,
            $orderRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\PaypalContext');
    }

    function it_is_a_context()
    {
        $this->shouldImplement(Context::class);
    }

    function it_checks_redirection_from_paypal_express_checkout(
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
    ) {
        $paypalExpressCheckoutPage->isOpen()->willReturn(true);

        $this->iShouldBeRedirectedToPaypalExpressCheckoutPage();
    }

    function it_throws_not_equal_exception_when_redirection_from_paypal_express_checkout_fails(
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage
    ) {
        $paypalExpressCheckoutPage->isOpen()->willReturn(false);

        $this->shouldThrow(NotEqualException::class)->during('iShouldBeRedirectedToPaypalExpressCheckoutPage');
    }

    function it_logs_in_and_pay_on_paypal_page(
        PaypalExpressCheckoutPageInterface $paypalExpressCheckoutPage,
        PaypalApiMocker $paypalApiMocker
    ) {
        $paypalApiMocker->mockApiSuccessfulPaymentResponse()->shouldBeCalled();
        $paypalExpressCheckoutPage->pay()->shouldBeCalled();

        $this->iSignInToPaypalAndPaySuccessfully();
    }

    function it_cancels_payment($paypalExpressCheckoutPage)
    {
        $paypalExpressCheckoutPage->cancel()->shouldBeCalled();

        $this->iCancelMyPaypalPayment();
    }

    function it_tries_to_pay_again(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        PaypalApiMocker $paypalApiMocker,
        OrderPaymentsPageInterface $orderPaymentsPage,
        OrderInterface $order,
        CustomerInterface $customer,
        UserInterface $user,
        PaymentInterface $payment
    ) {
        $sharedStorage->get('user')->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $orderRepository->findByCustomer($customer)->willReturn([$order]);
        $order->getLastPayment()->willReturn($payment);
        $paypalApiMocker->mockApiPaymentInitializeResponse()->shouldBeCalled();
        $orderPaymentsPage->clickPayButtonForGivenPayment($payment)->shouldBeCalled();

        $this->iTryToPayAgain();
    }

    function it_throws_runtime_exception_if_cannot_find_last_order_for_given_customer(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $sharedStorage->get('user')->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $orderRepository->findByCustomer($customer)->willReturn([]);

        $this->shouldThrow(\RuntimeException::class)->during('iTryToPayAgain');
    }
}
