<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

/**
 * Schema registry interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface SchemaRegistryInterface
{
    /**
     * Get an array of all registered schemas.
     *
     * @return array
     */
    public function getSchemas();

    /**
     * Register a schema for given settings namespace.
     *
     * @param string          $namespace
     * @param SchemaInterface $schema
     */
    public function registerSchema($namespace, SchemaInterface $schema);

    /**
     * Unregister schema with given namespace.
     *
     * @param string $namespace
     */
    public function unregisterSchema($namespace);

    /**
     * Has schema registered to given namespace?
     *
     * @param string $namespace
     *
     * @return Boolean
     */
    public function hasSchema($namespace);

    /**
     * Get schema for given namespace.
     *
     * @param string $namespace
     *
     * @return SchemaInterface
     */
    public function getSchema($namespace);
}
