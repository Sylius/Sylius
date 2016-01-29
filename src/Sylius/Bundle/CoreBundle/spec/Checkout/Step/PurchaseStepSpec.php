<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout\Step;

use Doctrine\Common\Persistence\ObjectManager;
use Payum\Core\GatewayInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Checkout\Step\CheckoutStep;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\FlowBundle\Process\Step\ActionResult;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Symfony\Bridge\Doctrine\RegistryInterface as DoctrinRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

require_once __DIR__.'/../../Fixture/RequestStack.php';

class PurchaseStepSpec extends ObjectBehavior
{
    function let(
        ContainerInterface $container,
        HttpRequestVerifierInterface $httpRequestVerifier,
        TokenInterface $token,
        Request $request,
        CartProviderInterface $cartProvider,
        RegistryInterface $payum,
        GatewayInterface $gateway,
        EventDispatcherInterface $eventDispatcher,
        DoctrinRegistryInterface $doctrine,
        ObjectManager $objectManager,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $session->getFlashBag()->willReturn($flashBag);
        $doctrine->getManager()->willReturn($objectManager);
        $token->getGatewayName()->willReturn('aGatewayName');
        $payum->getGateway('aGatewayName')->willReturn($gateway);
        $httpRequestVerifier->verify($request)->willReturn($token);
        $httpRequestVerifier->invalidate($token)->willReturn(null);

        $container->get('payum.security.http_request_verifier')->willReturn($httpRequestVerifier);
        $container->get('sylius.cart_provider')->willReturn($cartProvider);
        $container->get('payum')->willReturn($payum);
        $container->get('event_dispatcher')->willReturn($eventDispatcher);
        $container->get('session')->willReturn($session);
        $container->get('doctrine')->willReturn($doctrine);
        $container->has('doctrine')->willReturn(true);
        $container->get('translator')->willReturn($translator);

        $this->setName('purchase');

        $this->setContainer($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\Step\PurchaseStep');
    }

    function it_extends_checkout_step()
    {
        $this->shouldImplement(CheckoutStep::class);
    }

    function it_must_dispatch_pre_and_post_payment_state_changed_if_state_changed(
        Request $request,
        ProcessContextInterface $context,
        GatewayInterface $gateway,
        EventDispatcherInterface $eventDispatcher
    ) {
        $context->getRequest()->willReturn($request);

        $order = new Order();
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_NEW);
        $paymentModel->setOrder($order);
        $order->addPayment($paymentModel);

        $gateway
            ->execute(Argument::type(GetStatus::class))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markCaptured();
                $args[0]->setModel($paymentModel);
            }
        );

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_INITIALIZE,
                Argument::type(GenericEvent::class)
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE,
                Argument::type(GenericEvent::class)
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_COMPLETE,
                Argument::type(PurchaseCompleteEvent::class)
            )
            ->shouldBeCalled()
        ;

        $this->forwardAction($context)->shouldReturnAnInstanceOf(ActionResult::class);
    }

    function it_must_not_dispatch_pre_and_post_payment_state_changed_if_state_not_changed(
        Request $request,
        ProcessContextInterface $context,
        GatewayInterface $gateway,
        EventDispatcherInterface $eventDispatcher
    ) {
        $context->getRequest()->willReturn($request);

        $order = new Order();
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_COMPLETED);
        $paymentModel->setOrder($order);
        $order->addPayment($paymentModel);

        $gateway
            ->execute(Argument::type(GetStatus::class))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markCaptured();
                $args[0]->setModel($paymentModel);
            }
        );

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_INITIALIZE,
                Argument::type(GenericEvent::class)
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE,
                Argument::type(GenericEvent::class)
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_COMPLETE,
                Argument::type(PurchaseCompleteEvent::class)
            )
            ->shouldBeCalled()
        ;

        $this->forwardAction($context)->shouldReturnAnInstanceOf(ActionResult::class);
    }
}
