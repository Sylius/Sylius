<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductAssociationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ProductAssociationTypeChoiceType::class, [
                'label' => 'sylius.form.product_association.type',
            ])
            ->add('product', ProductChoiceType::class, [
                'label' => 'sylius.form.product_association.product',
                'property_path' => 'associatedProducts',
                'multiple' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_association';
    }
}
