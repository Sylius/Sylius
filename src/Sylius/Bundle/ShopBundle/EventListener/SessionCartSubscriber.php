<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

final class SessionCartSubscriber implements EventSubscriberInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var CartStorageInterface
     */
    private $cartStorage;

    /**
     * @param CartContextInterface $cartContext
     * @param CartStorageInterface $cartStorage
     */
    public function __construct(CartContextInterface $cartContext, CartStorageInterface $cartStorage)
    {
        $this->cartContext = $cartContext;
        $this->cartStorage = $cartStorage;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $session = $event->getRequest()->getSession();
        if ($session && !$session->isStarted()) {
            return;
        }

        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();
            Assert::isInstanceOf($cart, OrderInterface::class);
        } catch (CartNotFoundException $exception) {
            return;
        }

        if (null !== $cart && null !== $cart->getId() && null !== $cart->getChannel()) {
            $this->cartStorage->setForChannel($cart->getChannel(), $cart);
        }
    }
}
