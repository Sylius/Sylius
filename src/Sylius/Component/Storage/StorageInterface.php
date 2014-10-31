<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Storage;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface StorageInterface
{
    /**
     * Returns data from storage or the default one.
     *
     * @param string $key
     * @param mixed  $default
     */
    public function getData($key, $default);

    /**
     * Sets data in storage.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setData($key, $value);
}
