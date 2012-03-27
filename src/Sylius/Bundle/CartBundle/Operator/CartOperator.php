<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Operator;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\CartManagerInterface;
use Sylius\Bundle\CartBundle\Model\ItemInterface;

/**
 * Base class for cart operator.
 * Sensible defaults for most common cart solutions.
 * Can be overriden to fit exact needs.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class CartOperator implements CartOperatorInterface
{
    /**
     * Cart manager.
     *
     * @var CartManagerInterface
     */
    protected $cartManager;

    /**
     * Constructor.
     *
     * @param CartManagerInterface $cartManager;
     */
    public function __construct(CartManagerInterface $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(CartInterface $cart, ItemInterface $item)
    {
        foreach ($cart->getItems() as $existingItem) {
            if ($item->equals($existingItem)) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());

                return;
            }
        }

        $cart->addItem($item);
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(CartInterface $cart, ItemInterface $item)
    {
        $cart->removeItem($item);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(CartInterface $cart)
    {
        $cart->setTotalItems($cart->countItems());
    }

    /**
     * {@inheritdoc}
     */
    public function validate(CartInterface $cart)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(CartInterface $cart)
    {
        $this->cartManager->removeCart($cart);
    }

    /**
     * {@inheritdoc}
     */
    public function save(CartInterface $cart)
    {
        $this->cartManager->persistCart($cart);
    }
}
