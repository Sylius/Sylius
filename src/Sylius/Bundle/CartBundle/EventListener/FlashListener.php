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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Sylius\Bundle\CartBundle\SyliusCartEvents;

/**
 * Flash message listener.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class FlashListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    public $messages = array(
        SyliusCartEvents::CART_SAVE_COMPLETED    => 'The cart have been updated correctly.',
        SyliusCartEvents::CART_CLEAR_COMPLETED   => 'The cart has been successfully cleared.',

        SyliusCartEvents::ITEM_ADD_COMPLETED    => 'Item has been added to cart.',
        SyliusCartEvents::ITEM_REMOVE_COMPLETED => 'Item has been removed from cart.',

        SyliusCartEvents::ITEM_ADD_ERROR        => 'Error occurred while adding item to cart.',
        SyliusCartEvents::ITEM_REMOVE_ERROR     => 'Error occurred while removing item from cart.',
    );

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            SyliusCartEvents::CART_CLEAR_COMPLETED  => 'addSuccessFlash',
            SyliusCartEvents::CART_SAVE_COMPLETED   => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_COMPLETED    => 'addSuccessFlash',
            SyliusCartEvents::ITEM_REMOVE_COMPLETED => 'addSuccessFlash',

            SyliusCartEvents::ITEM_ADD_ERROR        => 'addErrorFlash',
            SyliusCartEvents::ITEM_REMOVE_ERROR     => 'addErrorFlash',
        );
    }

    public function addErrorFlash(Event $event)
    {
        $this->session->getFlashBag()->add('error', $event->getMessage() ?: $this->messages[$event->getName()]);
    }

    public function addSuccessFlash(Event $event)
    {
        $this->session->getFlashBag()->add('success', $event->getMessage() ?: $this->messages[$event->getName()]);
    }
}
