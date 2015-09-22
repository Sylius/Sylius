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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface SchemaRegistryInterface
{
    /**
     * @return array
     */
    public function getSchemas();

    /**
     * @param string          $namespace
     * @param SchemaInterface $schema
     */
    public function registerSchema($namespace, SchemaInterface $schema);

    /**
     * @param string $namespace
     */
    public function unregisterSchema($namespace);

    /**
     * @param string $namespace
     *
     * @return bool
     */
    public function hasSchema($namespace);

    /**
     * @param string $namespace
     *
     * @return SchemaInterface
     */
    public function getSchema($namespace);
}
