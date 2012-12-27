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
    public function getSchemas();
    public function registerSchema(SchemaInterface $schema);
    public function unregisterSchema($namespace);
    public function hasSchema($namespace);
    public function getSchema($namespace);
}
