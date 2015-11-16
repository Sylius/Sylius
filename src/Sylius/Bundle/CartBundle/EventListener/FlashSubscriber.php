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

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartEvents;
use Sylius\Component\Cart\Event\CartItemEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Flash message listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlashSubscriber implements EventSubscriberInterface
{
    const FLASHES_BAG = 'flashes';
    const TRANSLATION_DOMAIN = 'flashes';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param SessionInterface $session
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
        return array(
            CartEvents::POST_CLEAR => 'addSuccessFlash',
            CartEvents::POST_SAVE  => 'addSuccessFlash',

            CartItemEvents::POST_ADD      => 'addSuccessFlash',
            CartItemEvents::ADD_FAILED    => 'addErrorFlash',
            CartItemEvents::POST_REMOVE   => 'addSuccessFlash',
            CartItemEvents::REMOVE_FAILED => 'addErrorFlash',
        );
    }

    /**
     * @return $this
     */
    public static function getMessages()
    {
        return array(
            CartEvents::POST_CLEAR => 'sylius.cart.clear',
            CartEvents::POST_SAVE  => 'sylius.cart.save',

            CartItemEvents::POST_ADD      => 'sylius.cart_item.add',
            CartItemEvents::ADD_FAILED    => 'sylius.cart_item.add_failed',
            CartItemEvents::POST_REMOVE   => 'sylius.cart_item.remove',
            CartItemEvents::REMOVE_FAILED => 'sylius.cart_item.remove_failed',
        );
    }

    /**
     * @param CartEvent $event
     */
    public function addErrorFlash(CartEvent $event)
    {
        $this->addFlash('error', $event->getMessage(), $event->getName());
    }

    /**
     * @param CartEvent $event
     */
    public function addSuccessFlash(CartEvent $event)
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
        $messages = self::getMessages();

        $this->session->getBag(self::FLASHES_BAG)->add(
            $type,
            $this->translator->trans($message ?: $messages[$eventName], array(), self::TRANSLATION_DOMAIN)
        );
    }
}
