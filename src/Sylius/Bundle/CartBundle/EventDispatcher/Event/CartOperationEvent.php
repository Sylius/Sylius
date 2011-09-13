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

use Sylius\Bundle\CartBundle\Model\ItemInterface;

use Sylius\Bundle\CartBundle\Model\CartInterface;

final class CartOperationEvent extends FilterCartEvent
{
    protected $item;
    
    public function __construct(ItemInterface $item, CartInterface $cart)
    {
        $this->item = $item;
        
        parent::__construct($cart);
    }
    
    public function getItem()
    {
        return $this->item;
    }
}