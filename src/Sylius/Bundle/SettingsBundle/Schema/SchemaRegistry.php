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
 * Default schema registry.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SchemaRegistry implements SchemaRegistryInterface
{
    /**
     * Schemas.
     *
     * @var array
     */
    protected $schemas;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->schemas = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemas()
    {
        return $this->schemas;
    }

    /**
     * {@inheritdoc}
     */
    public function registerSchema($namespace, SchemaInterface $schema)
    {
        if ($this->hasSchema($namespace)) {
            throw new \InvalidArgumentException(sprintf('Schema with namespace "%s" has been already registered', $namespace));
        }

        $this->schemas[$namespace] = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterSchema($namespace)
    {
        if (!$this->hasSchema($namespace)) {
            throw new \InvalidArgumentException(sprintf('Schema with namespace "%s" does not exist', $namespace));
        }

        unset($this->schemas[$namespace]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSchema($namespace)
    {
        return isset($this->schemas[$namespace]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema($namespace)
    {
        if (!$this->hasSchema($namespace)) {
            throw new \InvalidArgumentException(sprintf('Schema with namespace "%s" does not exist', $namespace));
        }

        return $this->schemas[$namespace];
    }
}
