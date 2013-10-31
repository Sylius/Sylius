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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SchemaRegistryInterface
{
    /**
     * Get an array of all registered schemas.
     *
     * @return SchemaInterface[]
     */
    public function getSchemas();

    /**
     * Register a schema with optional namespace link.
     *
     * @param SchemaInterface $schema
     * @param null|string     $namespace
     */
    public function registerSchema(SchemaInterface $schema, $namespace = null);

    /**
     * Unregister schema with given alias.
     *
     * @param string      $alias
     * @param null|string $namespace
     */
    public function unregisterSchema($alias, $namespace = null);

    /**
     * Has schema registered to given alias?
     *
     * @param string      $alias
     * @param null|string $namespace
     *
     * @return bool
     */
    public function hasSchema($alias, $namespace = null);

    /**
     * Get schema for given alias.
     *
     * @param string      $alias
     * @param null|string $namespace
     *
     * @return SchemaInterface
     */
    public function getSchema($alias, $namespace = null);
}
