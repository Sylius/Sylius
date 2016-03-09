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

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Settings implements SettingsInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var string
     */
    protected $schemaAlias;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchemaAlias()
    {
        return $this->schemaAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchemaAlias($schemaAlias)
    {
        if (null !== $this->schemaAlias) {
            throw new \LogicException('The schema alias of the settings model is immutable, instantiate a new object in order to use another schema.');
        }

        $this->schemaAlias = $schemaAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function setNamespace($namespace)
    {
        if (null !== $this->namespace) {
            throw new \LogicException('The namespace of the settings model is immutable, instantiate a new object in order to use another namespace.');
        }

        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new ParameterNotFoundException($name);
        }

        return $this->parameters[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        if (!$this->has($name)) {
            throw new ParameterNotFoundException($name);
        }

        unset($this->parameters[$name]);
    }
}
