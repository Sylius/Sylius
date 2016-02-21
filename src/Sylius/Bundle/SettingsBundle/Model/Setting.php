<?php

namespace Sylius\Bundle\SettingsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class Setting implements SettingInterface
{
    /**
     * @var string
     */
    protected $schema;

    /**
     * @var ArrayCollection
     */
    protected $parameters;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
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
        foreach ($this->parameters as $parameter) {
            if ($name === $parameter->getName()) {
                return $parameter;
            }
        }

        throw new \InvalidArgumentException(sprintf('Parameter with name "%s" does not exist.', $name));
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(ParameterInterface $parameter)
    {
        return $this->parameters->contains($parameter);
    }

    /**
     * {@inheritdoc}
     */
    public function addParameter(ParameterInterface $parameter)
    {
        if (!$this->hasParameter($parameter)) {
            $this->parameters->add($parameter);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeParameter(ParameterInterface $parameter)
    {
        if ($this->hasParameter($parameter)) {
            return $this->parameters->removeElement($parameter);
        }
    }
}
