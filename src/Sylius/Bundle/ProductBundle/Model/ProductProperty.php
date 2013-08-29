<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Model;

/**
 * Property to product relation.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductProperty implements ProductPropertyInterface
{
    /**
     * Id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Product.
     *
     * @var ProductInterface
     */
    protected $product;

    /**
     * Property.
     *
     * @var PropertyInterface
     */
    protected $property;

    /**
     * Property value.
     *
     * @var mixed
     */
    protected $value;

    public function __toString()
    {
        return $this->value;
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
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function setProduct(ProductInterface $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * {@inheritdoc}
     */
    public function setProperty(PropertyInterface $property)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        if ($this->property && PropertyTypes::CHECKBOX === $this->property->getType()) {
            return (boolean) $this->value;
        }

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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $this->assertPropertyIsSet();

        return $this->property->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getPresentation()
    {
        $this->assertPropertyIsSet();

        return $this->property->getPresentation();
    }

    public function getType()
    {
        $this->assertPropertyIsSet();

        return $this->property->getType();
    }

    public function getConfiguration()
    {
        $this->assertPropertyIsSet();

        return $this->property->getConfiguration();
    }

    /**
     * @throws \BadMethodCallException When property is not set
     */
    protected function assertPropertyIsSet()
    {
        if (null === $this->property) {
            throw new \BadMethodCallException('The property have not been created yet so you cannot access proxy methods.');
        }
    }
}
