<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Association\Model\AbstractAssociation;
use Sylius\Component\Association\Model\AssociationTypeInterface;

/**
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductAssociation extends AbstractAssociation implements AssociationInterface
{
    /**
     * @var ProductInterface
     */
    private $product;

    /**
     * @param ProductInterface $product
     * @param AssociationTypeInterface $type
     */
    public function __construct(ProductInterface $product, AssociationTypeInterface $type)
    {
        parent::__construct($type);
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Product\Model\ProductInterface'              => 'sylius.model.product.class',
            'Sylius\Component\Product\Model\ProductTranslationInterface'   => 'sylius.model.product_translation.class',
            'Sylius\Component\Product\Model\AttributeInterface'            => 'sylius.model.product_attribute.class',
            'Sylius\Component\Product\Model\AttributeTranslationInterface' => 'sylius.model.product_attribute_translation.class',
            'Sylius\Component\Product\Model\AttributeValueInterface'       => 'sylius.model.product_attribute_value.class',
            'Sylius\Component\Product\Model\VariantInterface'              => 'sylius.model.product_variant.class',
            'Sylius\Component\Product\Model\OptionInterface'               => 'sylius.model.product_option.class',
            'Sylius\Component\Product\Model\OptionValueInterface'          => 'sylius.model.product_option_value.class',
            'Sylius\Component\Product\Model\ArchetypeInterface'            => 'sylius.model.product_archetype.class',
        );
    }

    /**
     * {@inheritdoc}
     *
     * @return ProductInterface
     */
    public function getAssociatedObject()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     */
    public function setAssociatedObject(ProductInterface $product)
    {
        $this->product = $product;
    }
}
