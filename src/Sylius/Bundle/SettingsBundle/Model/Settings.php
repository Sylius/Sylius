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

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var ArrayCollection|ParameterInterface[]
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
    }

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
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        if (!$this->hasParameter($name)) {
            throw new \InvalidArgumentException(sprintf('Parameter with name "%s" does not exist.', $name));
        }

        return $this->parameters->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        return $this->parameters->containsKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function addParameter(ParameterInterface $parameter)
    {
        $parameter->setSettings($this);
        $this->parameters->set($parameter->getName(), $parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function removeParameter(ParameterInterface $parameter)
    {
        $this->parameters->removeElement($parameter);
    }
}
