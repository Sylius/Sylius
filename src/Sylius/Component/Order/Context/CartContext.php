<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Context;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

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
    public function getCart(): OrderInterface
    {
        return $this->cartFactory->createNew();
    }
}
