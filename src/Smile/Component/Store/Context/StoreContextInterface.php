<?php

namespace Smile\Component\Store\Context;


use Smile\Component\Store\Model\StoreInterface;

interface StoreContextInterface
{
    /**
     * Get default store
     * @return \Smile\Component\Store\Model\StoreInterface
     */
    public function getDefaultStore();

    /**
     * Set the default store.
     * @param \Smile\Component\Store\Model\StoreInterface $store
     * @return void
     */
    public function setDefaultStore(StoreInterface $store);

    /**
     * Get the currently active store.
     * @return \Smile\Component\Store\Model\StoreInterface
     */
    public function getStore();

    /**
     * Set the currently active store.
     * @param \Smile\Component\Store\Model\StoreInterface $store
     * @return void
     */
    public function setStore(StoreInterface $store = null);
}