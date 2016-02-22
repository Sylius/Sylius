<?php

namespace Sylius\Bundle\SettingsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    protected $schema;

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
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema($schema)
    {
        if (null !== $this->schema) {
            throw new \LogicException('A settings schema is immutable, you have to define a new "Setting" object to use another schema.');
        }

        $this->schema = $schema;
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
