<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Manager;

use Sylius\Bundle\ResourceBundle\Doctrine\DomainManager;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\SyliusCartEvents;

class CartManager extends DomainManager
{
    /**
     * {@inheritdoc}
     */
    public function create($resource = null, $eventName = 'create', $flush = true, $transactional = true)
    {
        /** @var $cart CartInterface */
        $cart = $this->createNew();

        $this->eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, new CartEvent($cart));

        return parent::create($cart, $eventName, $flush, $transactional);
    }
}
