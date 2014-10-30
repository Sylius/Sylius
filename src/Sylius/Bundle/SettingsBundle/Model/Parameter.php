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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Parameter implements ParameterInterface
{
    /**
     * Parameter id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Parameter settings namespace.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Parameter name.
     *
     * @var string
     */
    protected $name;

    /**
     * Parameter value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Get parameter id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
