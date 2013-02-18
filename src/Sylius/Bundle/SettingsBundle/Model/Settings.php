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
 * Settings parameter model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Settings implements SettingsInterface
{
    /**
     * Parameters.
     *
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
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
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter with name "%s" does not exist.', $name));
        }

        return $this->parameters[$name];
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
    public function has($name)
    {
        return isset($this->parameters[$name]);
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
        unset($this->parameters[$offset]);
    }
}
