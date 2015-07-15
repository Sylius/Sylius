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
use Payum\Core\PaymentInterface;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use spec\Sylius\Bundle\CoreBundle\Fixture\RequestStack;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Symfony\Bridge\Doctrine\RegistryInterface as DoctrinRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

require_once __DIR__.'/../../Fixture/RequestStack.php';

class PurchaseStepSpec extends ObjectBehavior
{
    public function let(
        ContainerInterface $container,
        HttpRequestVerifierInterface $httpRequestVerifier,
        TokenInterface $token,
        Request $request,
        CartProviderInterface $cartProvider,
        RegistryInterface $payum,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        DoctrinRegistryInterface $doctrine,
        ObjectManager $objectManager,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        FactoryInterface $factory
    ) {
        $session->getFlashBag()->willReturn($flashBag);
        $doctrine->getManager()->willReturn($objectManager);
        $token->getPaymentName()->willReturn('aPaymentName');
        $payum->getPayment('aPaymentName')->willReturn($payment);
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
        $container->get('sm.factory')->willReturn($factory);

        $this->setName('purchase');

        $this->setContainer($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Checkout\Step\PurchaseStep');
    }

    public function it_extends_checkout_step()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Checkout\Step\CheckoutStep');
    }

    public function it_must_dispatch_pre_and_post_payment_state_changed_if_state_changed(
        Request $request,
        $factory,
        ProcessContextInterface $context,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $sm
    ) {
        $context->getRequest()->willReturn($request);

        $order = new Order();
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_NEW);
        $paymentModel->setOrder($order);
        $order->addPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\GetStatus'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markCaptured();
                $args[0]->setModel($paymentModel);
            }
        );

        $factory->get($paymentModel, PaymentTransitions::GRAPH)->willReturn($sm);
        $sm->getTransitionToState('completed')->willReturn(PaymentTransitions::SYLIUS_COMPLETE);
        $sm->apply(PaymentTransitions::SYLIUS_COMPLETE)->shouldBeCalled();

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_INITIALIZE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_COMPLETE,
                Argument::type('Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent')
            )
            ->shouldBeCalled()
        ;

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }

    public function it_must_not_dispatch_pre_and_post_payment_state_changed_if_state_not_changed(
        Request $request,
        $factory,
        ProcessContextInterface $context,
        PaymentInterface $payment,
        EventDispatcherInterface $eventDispatcher,
        StateMachineInterface $sm
    ) {
        $context->getRequest()->willReturn($request);

        $order = new Order();
        $paymentModel = new Payment();
        $paymentModel->setState(Payment::STATE_COMPLETED);
        $paymentModel->setOrder($order);
        $order->addPayment($paymentModel);

        $payment
            ->execute(Argument::type('Sylius\Bundle\PayumBundle\Payum\Request\GetStatus'))
            ->will(function ($args) use ($order, $paymentModel) {
                $args[0]->markCaptured();
                $args[0]->setModel($paymentModel);
            }
        );

        $factory->get($paymentModel, PaymentTransitions::GRAPH)->willReturn($sm);
        $sm->getTransitionToState('completed')->willReturn(null);
        $sm->apply(PaymentTransitions::SYLIUS_COMPLETE)->shouldNotBeCalled();

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_INITIALIZE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_PRE_COMPLETE,
                Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
            )
            ->shouldBeCalled()
        ;

        $eventDispatcher
            ->dispatch(
                SyliusCheckoutEvents::PURCHASE_COMPLETE,
                Argument::type('Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent')
            )
            ->shouldBeCalled()
        ;

        $this->forwardAction($context)->shouldReturnAnInstanceOf('Sylius\Bundle\FlowBundle\Process\Step\ActionResult');
    }
}
