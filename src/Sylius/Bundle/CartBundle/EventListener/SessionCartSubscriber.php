<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventListener;

use Sylius\Component\Cart\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SessionCartSubscriber implements EventSubscriberInterface
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @var string
     */
    private $sessionKeyName;

    /**
     * @param CartContextInterface $cartContext
     * @param string $sessionKeyName
     */
    public function __construct(CartContextInterface $cartContext, $sessionKeyName)
    {
        $this->cartContext = $cartContext;
        $this->sessionKeyName = $sessionKeyName;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Hacky hack. Until there is a better solution.
        if ('html' !== $request->getRequestFormat()) {
            return;
        }

        $cart = $this->cartContext->getCart();

        if (null !== $cart && null !== $cart->getId()) {
            $request->getSession()->set($this->sessionKeyName, $cart->getId());
        }
    }
}
