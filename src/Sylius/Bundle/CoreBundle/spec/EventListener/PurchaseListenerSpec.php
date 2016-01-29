<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class PurchaseListenerSpec extends ObjectBehavior
{
    function let(
        CartProviderInterface $cartProvider,
        UrlGeneratorInterface $router,
        SessionInterface $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $this->beConstructedWith($cartProvider, $router, $session, $translator, 'sylius_checkout_payment');

        $session->getBag('flashes')->willReturn($flashBag);
        $event->getSubject()->willReturn($payment);
        $router->generate('sylius_checkout_payment')->willReturn('/payment');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\PurchaseListener');
    }

    function it_should_abandon_cart_if_payment_status_success(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_success_flash_message_if_payment_status_success(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $translator
            ->trans('sylius.checkout.success', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.success')
        ;
        $flashBag->add('success', 'translated.sylius.checkout.success')->shouldBeCalled();

        $this->addFlash($event);
    }

    function it_should_abandon_cart_if_payment_status_pending(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_PENDING);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_notice_flash_message_if_payment_status_pending(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_PENDING);

        $translator
            ->trans('sylius.checkout.processing', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.processing')
        ;
        $flashBag->add('notice', 'translated.sylius.checkout.processing')->shouldBeCalled();

        $this->addFlash($event);
    }

    function it_should_abandon_cart_if_payment_status_processing(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_notice_flash_message_if_payment_status_processing(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);

        $translator
            ->trans('sylius.checkout.processing', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.processing')
        ;
        $flashBag->add('notice', 'translated.sylius.checkout.processing')->shouldBeCalled();

        $this->addFlash($event);
    }

    function it_should_not_abandon_cart_if_payment_status_canceled(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_VOID);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_notice_flash_message_if_payment_status_canceled(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_VOID);

        $translator
            ->trans('sylius.checkout.canceled', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.canceled')
        ;
        $flashBag->add('notice', 'translated.sylius.checkout.canceled')->shouldBeCalled();

        $this->addFlash($event);
    }

    function it_should_not_abandon_cart_if_payment_status_failed(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_FAILED);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_error_flash_message_if_payment_status_failed(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_FAILED);

        $translator
            ->trans('sylius.checkout.failed', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.failed')
        ;
        $flashBag->add('error', 'translated.sylius.checkout.failed')->shouldBeCalled();

        $this->addFlash($event);
    }

    function it_should_not_abandon_cart_if_payment_status_unknown(
        CartProviderInterface $cartProvider,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_UNKNOWN);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_set_error_flash_message_if_payment_status_unknown(
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        PurchaseCompleteEvent $event,
        PaymentInterface $payment
    ) {
        $payment->getState()->willReturn(PaymentInterface::STATE_UNKNOWN);

        $translator
            ->trans('sylius.checkout.unknown', [], 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.unknown')
        ;
        $flashBag->add('error', 'translated.sylius.checkout.unknown')->shouldBeCalled();

        $this->addFlash($event);
    }
}
