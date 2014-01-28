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
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\PayumBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

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
        $this->beConstructedWith($cartProvider, $router, $session, $translator);

        $session->getBag('flashes')->willReturn($flashBag);
        $event->getSubject()->willReturn($payment);
        $router->generate('sylius_checkout_payment')->willReturn('/payment');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\PurchaseListener');
    }

    function it_should_abandon_cart_and_set_success_flash_message_if_payment_status_success(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_COMPLETED);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $translator
            ->trans('sylius.checkout.success', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.success')
        ;
        $flashBag->add('success','translated.sylius.checkout.success')->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_abandon_cart_and_set_notice_flash_message_if_payment_status_pending(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_PENDING);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $translator
            ->trans('sylius.checkout.processing', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.processing')
        ;
        $flashBag->add('notice','translated.sylius.checkout.processing')->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_abandon_cart_and_set_notice_flash_message_if_payment_status_processing(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_PROCESSING);

        $cartProvider->abandonCart()->shouldBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldNotBeCalled();

        $translator
            ->trans('sylius.checkout.processing', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.processing')
        ;
        $flashBag->add('notice','translated.sylius.checkout.processing')->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_not_abandon_cart_and_set_notice_flash_message_if_payment_status_canceled(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_VOID);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $translator
            ->trans('sylius.checkout.canceled', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.canceled')
        ;
        $flashBag->add('notice','translated.sylius.checkout.canceled')->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_not_abandon_cart_and_set_error_flash_message_if_payment_status_failed(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_FAILED);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $translator
            ->trans('sylius.checkout.failed', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.failed')
        ;
        $flashBag->add('error','translated.sylius.checkout.failed')->shouldBeCalled();

        $this->abandonCart($event);
    }

    function it_should_not_abandon_cart_and_set_error_flash_message_if_payment_status_unknown(CartProviderInterface $cartProvider, TranslatorInterface $translator, FlashBagInterface $flashBag, PurchaseCompleteEvent $event, PaymentInterface $payment)
    {
        $payment->getState()->willReturn(PaymentInterface::STATE_UNKNOWN);

        $cartProvider->abandonCart()->shouldNotBeCalled();

        $event->setResponse(new RedirectResponse('/payment'))->shouldBeCalled();

        $translator
            ->trans('sylius.checkout.unknown', array(), 'flashes')
            ->shouldBeCalled()
            ->willReturn('translated.sylius.checkout.unknown')
        ;
        $flashBag->add('error','translated.sylius.checkout.unknown')->shouldBeCalled();

        $this->abandonCart($event);
    }
}
