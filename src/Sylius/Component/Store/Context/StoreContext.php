<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Store\Context;


use Sylius\Component\Store\Model\StoreInterface;
use Sylius\Component\Storage\StorageInterface;

/**
 * Context holding store information
 *
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class StoreContext implements StoreContextInterface
{
    const STORAGE_KEY = '_smile.store';

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * Default store
     * @var StoreInterface
     */
    protected $defaultStore;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultStore()
    {
        return $this->defaultStore;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultStore(StoreInterface $store)
    {
        $this->defaultStore = $store;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStore()
    {
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultStore);
    }

    /**
     * {@inheritdoc}
     */
    public function setStore(StoreInterface $store = null)
    {
        return $this->storage->setData(self::STORAGE_KEY, $store);
    }
}