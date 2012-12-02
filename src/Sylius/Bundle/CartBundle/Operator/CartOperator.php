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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\CartItemInterface;

/**
 * Base class for cart operator.
 * Sensible defaults for most common cart solutions.
 * Can be overriden to fit exact needs.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartOperator implements CartOperatorInterface
{
    /**
     * Cart manager.
     *
     * @var ObjectManager
     */
    protected $cartManager;

    /**
     * Constructor.
     *
     * @param ObjectManager $cartManager;
     */
    public function __construct(ObjectManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(CartInterface $cart, CartItemInterface $item)
    {
        $cart->addItem($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(CartInterface $cart, CartItemInterface $item)
    {
        $cart->removeItem($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(CartInterface $cart)
    {
        $cart->calculateTotal();
        $cart->setTotalItems($cart->countItems());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(CartInterface $cart)
    {
        $this->cartManager->remove($cart);
        $this->cartManager->flush();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CartInterface $cart)
    {
        $this->cartManager->persist($cart);
        $this->cartManager->flush();

        return $this;
    }
}
