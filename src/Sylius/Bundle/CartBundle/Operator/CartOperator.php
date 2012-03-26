<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartsBundle\Operator;

use Sylius\Bundle\CartsBundle\Model\CartInterface;
use Sylius\Bundle\CartsBundle\Model\CartManagerInterface;
use Sylius\Bundle\CartsBundle\Model\ItemInterface;

/**
 * Base class for cart operator.
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
}
