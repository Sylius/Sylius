<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Accessor;

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\PropertyAccessor;

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
class ProductAccessor extends PropertyAccessor
{
    /**
     * {@inheritdoc}
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        try {
            return parent::getValue($objectOrArray, $propertyPath);
        } catch (Exception\NoSuchPropertyException $e) {
            $tags = array();
            if (!$objectOrArray instanceof ProductInterface) {
                return $tags;
            }

            $propertyPath = strtolower((string) $propertyPath);
            foreach ($objectOrArray->getAvailableVariants() as $variant) {
                foreach ($variant->getOptions() as $option) {
                    if ($propertyPath === strtolower($option->getPresentation())) {
                        $tags[] = $option->getValue();
                    }
                }
            }

            foreach ($objectOrArray->getAttributes() as $attribute) {
                if ($propertyPath === strtolower(str_replace(' ', '_', $attribute->getPresentation()))) {
                    $tags[] = $attribute->getValue();
                }
            }

            return array_values(array_unique($tags));
        }
    }
}
