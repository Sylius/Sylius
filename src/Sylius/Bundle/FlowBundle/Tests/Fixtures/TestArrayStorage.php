<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle\Tests\Fixtures;

use Sylius\Bundle\FlowBundle\Storage\StorageInterface;

class TestArrayStorage implements StorageInterface
{
    private $data = array();

    public function initialize($domain)
    {

    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function remove($key)
    {
        unset($this->data[$key]);
    }

    public function clear()
    {
        $this->data = array();
    }
}
