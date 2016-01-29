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

use Sylius\Component\Storage\StorageInterface as BaseStorageInterface;

/**
 * Storage interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
interface StorageInterface extends BaseStorageInterface
{
    /**
     * Initializes storage for given domain.
     *
     * @param string $domain
     *
     * @return $this
     */
    public function initialize($domain);

    /**
     * Clears all values from current domain.
     */
    public function clear();
}
