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

/**
 * Interface defining a store context
 *
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
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