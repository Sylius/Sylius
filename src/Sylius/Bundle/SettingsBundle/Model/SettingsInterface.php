<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Model;

/**
 * Settings interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface SettingsInterface extends \ArrayAccess
{
    /**
     * Get all parameters.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Set all parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * Get parameter by name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Set parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value);

    /**
     * Has parameter?
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function has($name);
}
