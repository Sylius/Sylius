<?php

namespace Sylius\Component\Store\Context;


use Sylius\Component\Store\Model\StoreInterface;

interface StoreContextInterface
{
    /**
     * Get default store
     * @return StoreInterface
     */
    public function getDefaultStore();

    /**
     * Set the default store.
     * @param StoreInterface $store
     * @return void
     */
    public function setDefaultStore(StoreInterface $store);

    /**
     * Get the currently active store.
     * @return StoreInterface
     */
    public function getStore();

    /**
     * Set the currently active store.
     * @param StoreInterface $store
     * @return void
     */
    public function setStore(StoreInterface $store = null);
}