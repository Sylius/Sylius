<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CheckoutResolver implements EventSubscriberInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var CheckoutStateUrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    /**
     * @var FactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @param CartContextInterface $cartContext
     * @param CheckoutStateUrlGeneratorInterface $urlGenerator
     * @param RequestMatcherInterface $requestMatcher
     * @param FactoryInterface $stateMachineFactory
     */
    public function __construct(
        CartContextInterface $cartContext,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
        RequestMatcherInterface $requestMatcher,
        FactoryInterface $stateMachineFactory
    ) {
        $this->cartContext = $cartContext;
        $this->urlGenerator = $urlGenerator;
        $this->requestMatcher = $requestMatcher;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->requestMatcher->matches($request)) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        if ($order->isEmpty()) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('sylius_shop_cart_summary')));
        }

        $stateMachine = $this->stateMachineFactory->get($order, $this->getRequestedGraph($request));

        if ($stateMachine->can($this->getRequestedTransition($request))) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generateForOrderCheckoutState($order)));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getRequestedGraph(Request $request)
    {
        return $request->attributes->get('_sylius')['state_machine']['graph'];
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getRequestedTransition(Request $request)
    {
        return $request->attributes->get('_sylius')['state_machine']['transition'];
    }

    /**
     * @param Request $request
     *
     * @return null|string
     */
    private function getReferer(Request $request)
    {
        return $request->headers->get('referer');
    }
}
