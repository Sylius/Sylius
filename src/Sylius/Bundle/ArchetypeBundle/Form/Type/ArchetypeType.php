<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Product archetype form type.
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.archetype.name'
            ))
            ->add('parent', 'sylius_archetype_choice', array(
                'required' => false,
                'label' => 'sylius.form.archetype.parent',
                'property' => 'name'
            ))
            ->add('attributes', 'sylius_archetype_attribute_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.archetype.attributes'
            ))
            ->add('options', 'sylius_archetype_option_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.archetype.options'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_archetype';
    }
}
