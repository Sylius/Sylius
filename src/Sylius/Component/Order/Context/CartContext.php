<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Context;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartContext implements CartContextInterface
{
    /**
     * @var FactoryInterface
     */
    private $cartFactory;

    /**
     * @param FactoryInterface $cartFactory
     */
    public function __construct(FactoryInterface $cartFactory)
    {
        $this->cartFactory = $cartFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->cartFactory->createNew();
    }
}
