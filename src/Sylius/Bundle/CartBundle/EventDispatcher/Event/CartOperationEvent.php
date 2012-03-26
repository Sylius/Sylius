<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventDispatcher\Event;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\ItemInterface;

/**
 * Cart operation event.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class CartOperationEvent extends FilterCartEvent
{
    /**
     * Cart item.
     *
     * @var ItemInterface
     */
    private $item;

    /**
     * Constructor.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     */
    public function __construct(CartInterface $cart, ItemInterface $item)
    {
        $this->item = $item;

        parent::__construct($cart);
    }

    /**
     * Returns item.
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
