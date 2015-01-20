<?php

namespace Smile\Bundle\StoreBundle\Context;


use Smile\Component\Store\Context\StoreContextInterface;
use Smile\Component\Store\Model\StoreInterface;
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StoreContext implements StoreContextInterface
{
    const STORAGE_KEY = '_smile.store';

    /**
     * @var SessionInterface
     */
    protected $storage;

    /**
     * Default store
     * @var \Smile\Component\Store\Model\StoreInterface
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