<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Accessor;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * This class uses the property accessor to dynamically translate getters to values.
 * In case of sylius it extends the default functionality to include options and attributes.
 *
 * As a result someone can use any attribute or option name and get the value of it, something like a
 * dynamic getter. This class is quite useful and maybe it belongs to the product bundle itself rather than here.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class ProductAccessor extends PropertyAccessor
{
    /**
     * {@inheritdoc}
     */
    public function getValue($product, $propertyPath)
    {
        try {
            return parent::getValue($product, $propertyPath);
        } catch (Exception\NoSuchPropertyException $e) {
            $tags = [];
            if (!$product instanceof ProductInterface) {
                return $tags;
            }

            $propertyPath = strtolower((string) $propertyPath);
            foreach ($product->getAvailableVariants() as $variant) {
                foreach ($variant->getOptions() as $option) {
                    if ($propertyPath === strtolower($option->getOptionCode())) {
                        $tags[] = $option->getValue();
                    }
                }
            }

            foreach ($product->getAttributes() as $attribute) {
                if ($propertyPath === strtolower($attribute->getName())) {
                    $tags[] = $attribute->getValue();
                }
            }

            return array_values(array_unique($tags));
        }
    }
}
