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

use Sylius\Bundle\SettingsBundle\Exception\ParameterNotFoundException;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
interface SettingsInterface extends ResourceInterface, \ArrayAccess, \Countable
{
    /**
     * @return string
     */
    public function getSchemaAlias();

    /**
     * @param string $schemaAlias
     */
    public function setSchemaAlias($schemaAlias);

    /**
     * @return string
     */
    public function getNamespace();

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters);

    /**
     * Get parameter by it's name.
     *
     * @param string $name
     *
     * @throws ParameterNotFoundException
     *
     * @return string
     */
    public function get($name);

    /**
     * Checks if it contains the given parameter.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Set a parameter.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value);

    /**
     * Removes a parameter.
     *
     * @param string $name
     *
     * @throws ParameterNotFoundException
     */
    public function remove($name);
}
