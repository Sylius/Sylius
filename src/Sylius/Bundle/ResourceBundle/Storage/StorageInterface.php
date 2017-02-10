<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Storage;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface StorageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * @param string $name
     */
    public function remove($name);

    /**
     * @return array
     */
    public function all();
}
