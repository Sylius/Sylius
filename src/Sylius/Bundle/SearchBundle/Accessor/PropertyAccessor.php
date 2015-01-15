<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Accessor;

use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\PropertyAccessor as BasePropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * This class uses the property accessor to dynamically translate getters to values.
 * In case of Sylius it extends the default functionality to include options and attributes.
 *
 * As a result someone can use any attribute or option name and get the value of it, something like a
 * dynamic getter. This class is quite useful and maybe it belongs to the product bundle itself rather than here.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class PropertyAccessor extends BasePropertyAccessor
{
    /**
     * @var PropertyAccessorInterface[]
     */
    protected $customAccessors;

    /**
     * @param PropertyAccessorInterface[] $customAccessors
     */
    public function __construct($customAccessors = array())
    {
        foreach ($customAccessors as $customAccessorClass) {
            $this->customAccessors[] = new $customAccessorClass;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        try {
            foreach ($this->customAccessors as $accessor) {
                return $accessor->getValue($objectOrArray, $propertyPath);
            }

            return parent::getValue($objectOrArray, $propertyPath);
        } catch (Exception\NoSuchPropertyException $e) {
            return null;
        }
    }
}
