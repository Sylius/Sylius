<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Context;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CartContext implements CartContextInterface
{
    /**
     * Cart storage.
     *
     * @var StorageInterface
     */
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCartIdentifier()
    {
        return $this->storage->getData(self::STORAGE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCartIdentifier(CartInterface $cart)
    {
        $this->storage->setData(self::STORAGE_KEY, $cart->getIdentifier());
    }

    /**
     * {@inheritdoc}
     */
    public function resetCurrentCartIdentifier()
    {
        $this->storage->removeData(self::STORAGE_KEY);
    }
}
