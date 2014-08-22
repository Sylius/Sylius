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
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

/**
 * Product Accessor
 *
 * This class uses the property accessor to dynamically translate getters to values.
 * In case of sylius it extends the default functionality to include options and attributes.
 *
 * As a result someone can use any attribute or option name and get the value of it, something like a
 * dynamic getter. This class is quite useful and maybe it belongs to the product bundle itself rather than here.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class PropertyAccessorAdaptor extends PropertyAccessor
{
    /**
     * @var
     */
    protected $customAccessors;

    /**
     * @param array $customAccessors
     */
    public function __construct($customAccessors = array())
    {
        foreach ($customAccessors as $customAccessorClass) {
            $this->customAccessors[] = new $customAccessorClass;
        }
    }

    /**
     * @param array|object                 $objectOrArray
     * @param string|PropertyPathInterface $propertyPath
     *
     * @return mixed
     * @throws \Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        try {
            foreach ($this->customAccessors as $accessor) {

                if ($accessor->canAccessObject($objectOrArray)) {
                    return $accessor->accessObject($objectOrArray, $propertyPath);
                }
            }

            return parent::getValue($objectOrArray, $propertyPath);

        }catch (Exception\NoSuchPropertyException $e) {
            return null;
        }

    }

}