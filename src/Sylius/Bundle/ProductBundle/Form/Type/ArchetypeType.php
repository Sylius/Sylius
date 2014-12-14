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

use Sylius\Bundle\ArchetypeBundle\Form\Type\ArchetypeType as BaseArchetypeType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product archetype form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArchetypeType extends BaseArchetypeType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.product_archetype.name'
            ))
            ->add('parent', 'sylius_product_archetype_choice', array(
                'required' => false,
                'label' => 'sylius.form.product_archetype.parent',
                'property' => 'name'
            ))
            ->add('attributes', 'sylius_product_attribute_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.product_archetype.attributes'
            ))
            ->add('options', 'sylius_product_option_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.product_archetype.options'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_archetype';
    }
}
