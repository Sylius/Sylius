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

use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Event\FlashEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Flash message listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashSubscriber implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    public $messages;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SessionInterface    $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SyliusCartEvents::CART_CLEAR_COMPLETED => 'addSuccessFlash',
            SyliusCartEvents::CART_SAVE_COMPLETED => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_COMPLETED => 'addSuccessFlash',
            SyliusCartEvents::ITEM_REMOVE_COMPLETED => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_ERROR => 'addErrorFlash',
            SyliusCartEvents::ITEM_REMOVE_ERROR => 'addErrorFlash',
        ];
    }

    /**
     * @return $this
     */
    public function setMessages()
    {
        $this->messages = [
            SyliusCartEvents::CART_SAVE_COMPLETED => 'sylius.cart.cart_save_completed',
            SyliusCartEvents::CART_CLEAR_COMPLETED => 'sylius.cart.cart_clear_completed',

            SyliusCartEvents::ITEM_ADD_COMPLETED => 'sylius.cart.item_add_completed',
            SyliusCartEvents::ITEM_REMOVE_COMPLETED => 'sylius.cart.item_remove_completed',

            SyliusCartEvents::ITEM_ADD_ERROR => 'sylius.cart.item_add_error',
            SyliusCartEvents::ITEM_REMOVE_ERROR => 'sylius.cart.item_remove_error',
        ];

        return $this;
    }

    /**
     * @param FlashEvent $event
     */
    public function addErrorFlash(FlashEvent $event)
    {
        $this->addFlash('error', $event->getMessage(), $event->getName());
    }

    /**
     * @param FlashEvent $event
     */
    public function addSuccessFlash(FlashEvent $event)
    {
        $this->addFlash('success', $event->getMessage(), $event->getName());
    }

    /**
     * @param string $type
     * @param string $message
     * @param string $eventName
     */
    private function addFlash($type, $message, $eventName)
    {
        $this->session->getBag('flashes')->add(
            $type,
            $message ?: $this->translator->trans($this->messages[$eventName], [], 'flashes')
        );
    }
}
