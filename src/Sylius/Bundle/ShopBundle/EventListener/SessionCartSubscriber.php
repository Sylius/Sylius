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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class SessionCartSubscriber implements EventSubscriberInterface
{
    public function __construct(private CartContextInterface $cartContext, private CartStorageInterface $cartStorage)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelResponse(ResponseEvent $event): void
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

        if (!$request->hasSession() || !$request->getSession()->isStarted()) {
            return;
        }

        try {
            $cart = $this->cartContext->getCart();

            /** @var OrderInterface $cart */
            Assert::isInstanceOf($cart, OrderInterface::class);
        } catch (CartNotFoundException) {
            return;
        }

        if (null !== $cart->getId() && null !== $cart->getChannel()) {
            $this->cartStorage->setForChannel($cart->getChannel(), $cart);
        }
    }
}
