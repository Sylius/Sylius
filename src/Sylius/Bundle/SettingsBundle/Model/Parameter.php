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
class Parameter implements ParameterInterface
{
    protected $id;
    protected $name;
    protected $namespace;
    protected $value;
    protected $modifiedAt;

    public function getId()
    {
        return $this->id;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getValue()
    {
        return null === $this->value ? null : unserialize($this->value);
    }

    public function setValue($value)
    {
        $this->value = serialize($value);
    }

    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }
}
