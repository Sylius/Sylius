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
 * Settings parameter interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
interface ParameterInterface
{
    /**
     * Get settings namespace.
     *
     * @return string
     */
    public function getNamespace();

    /**
     * Set settings namespace.
     *
     * @param string $namespace
     */
    public function setNamespace($namespace);

    /**
     * Get parameter name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set parameter name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get value.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Set parameter value.
     *
     * @param mixed $value
     */
    public function setValue($value);
}
