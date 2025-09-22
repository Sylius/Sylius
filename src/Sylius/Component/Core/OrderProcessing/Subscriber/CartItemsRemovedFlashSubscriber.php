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

namespace Sylius\Component\Core\OrderProcessing\Subscriber;

use Sylius\Component\Core\Event\CartItemsRemovedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartItemsRemovedFlashSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CartItemsRemovedEvent::class => 'onCartItemsRemoved',
        ];
    }

    public function onCartItemsRemoved(CartItemsRemovedEvent $event): void
    {
        $session = $this->requestStack->getSession();

        $message = $this->translator->trans(
            'sylius.cart.product.not_available',
            [
                '%count%' => $event->removedCount,
                '%items%' => implode(', ', $event->removedItemNames),
            ],
            'flashes',
        );

        $flashBag = $session->getBag('flashes');
        if (!$flashBag instanceof FlashBagInterface) {
            return;
        }

        $flashBag->add('warning', $message);
    }
}
