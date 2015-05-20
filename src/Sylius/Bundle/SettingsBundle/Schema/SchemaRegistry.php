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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SchemaRegistry implements SchemaRegistryInterface
{
    /**
     * Schemas.
     *
     * @var SchemaInterface[]
     */
    protected $schemas = array();

    /**
     * Schema namespaces.
     *
     * @var string[]
     */
    protected $namespaces = array();

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
    public function registerSchema(SchemaInterface $schema, $namespace = null)
    {
        $this->schemas[$schema->getAlias()] = $schema;
        if (null !== $namespace) {
            $this->namespaces[$namespace] = $schema->getAlias();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterSchema($alias, $namespace = null)
    {
        $this->resolveSchema($alias, $namespace);

        unset($this->schemas[$alias]);
        unset($this->namespaces[$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasSchema($alias, $namespace = null)
    {
        return $this->resolveSchema($alias, $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema($alias, $namespace = null)
    {
        return $this->resolveSchema($alias, $namespace, true);
    }

    /**
     * @param string      $alias
     * @param null|string $namespace
     * @param bool        $returnSchema
     *
     * @return bool|SchemaInterface
     *
     * @throws \InvalidArgumentException
     */
    private function resolveSchema($alias, $namespace = null, $returnSchema = false)
    {
        if (isset($this->schemas[$alias])) {
            return $returnSchema ? $this->schemas[$alias] : true;
        }

        if (null !== $namespace) {
            if (isset($this->namespaces[$namespace])) {
                return $returnSchema ? $this->schemas[$this->namespaces[$namespace]] : true;
            }

            throw new \InvalidArgumentException(sprintf('Schema with alias "%s" namespace "%s" does not exist.', $alias, $namespace));
        }

        throw new \InvalidArgumentException(sprintf('Schema with alias "%s" does not exist.', $alias));
    }
}
