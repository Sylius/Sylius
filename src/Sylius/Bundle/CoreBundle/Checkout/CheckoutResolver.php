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

namespace Sylius\Bundle\CoreBundle\Checkout;

use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CheckoutResolver implements EventSubscriberInterface
{
    public function __construct(
        private CartContextInterface $cartContext,
        private CheckoutStateUrlGeneratorInterface $urlGenerator,
        private RequestMatcherInterface $requestMatcher,
        private FactoryInterface $stateMachineFactory,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (\method_exists($event, 'isMainRequest')) {
            $isMainRequest = $event->isMainRequest();
        } else {
            /** @phpstan-ignore-next-line */
            $isMainRequest = $event->isMasterRequest();
        }
        if (!$isMainRequest) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->requestMatcher->matches($request)) {
            return;
        }

        /** @var OrderInterface $order */
        $order = $this->cartContext->getCart();
        if ($order->isEmpty()) {
            $event->setResponse(new RedirectResponse($this->urlGenerator->generateForCart()));

            return;
        }

        $graph = $this->getRequestedGraph($request);
        $transition = $this->getRequestedTransition($request);

        if (null === $graph || null === $transition) {
            return;
        }

        $stateMachine = $this->stateMachineFactory->get($order, $graph);

        if ($stateMachine->can($transition)) {
            return;
        }

        $event->setResponse(new RedirectResponse($this->urlGenerator->generateForOrderCheckoutState($order)));
    }

    private function getRequestedGraph(Request $request): ?string
    {
        return $request->attributes->get('_sylius', [])['state_machine']['graph'] ?? null;
    }

    private function getRequestedTransition(Request $request): ?string
    {
        return $request->attributes->get('_sylius', [])['state_machine']['transition'] ?? null;
    }
}
