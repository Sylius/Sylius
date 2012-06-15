<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Storage;

/**
 * Storage interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface StorageInterface
{
    /**
     * Initializes storage for given domain.
     *
     * @param strgin $domain
     */
    function initialize($domain);

    /**
     * Checks if the storage has a value for a key.
     *
     * @param string $key A unique key
     *
     * @return Boolean Whether the storage has a value for this key
     */
    function has($key);

    /**
     * Returns the value for a key.
     *
     * @param string $key A unique key
     * @param mixed  $default
     *
     * @return string|null The value in the storage or default if set or null if not found
     */
    function get($key, $default = null);

    /**
     * Sets a value in the storage.
     *
     * @param string $key   A unique key
     * @param string $value The value to storage
     */
    function set($key, $value);

    /**
     * Removes a value from the storage.
     *
     * @param string $key A unique key
     */
    function remove($key);

    /**
     * Clears all values from current domain.
     */
    function clear();
}
