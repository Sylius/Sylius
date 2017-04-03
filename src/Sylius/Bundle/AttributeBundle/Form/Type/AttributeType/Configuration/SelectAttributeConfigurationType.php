<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Sylius\Bundle\AttributeBundle\Form\EventSubscriber\ChangeStructureOfChoicesFormEventSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
class SelectAttributeConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choices', SelectAttributeChoicesCollectionType::class, [
                'entry_type' => TextType::class,
                'label' => 'sylius.form.attribute_type_configuration.select.values',
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('multiple', CheckboxType::class, [
                'label' => 'sylius.form.attribute_type_configuration.select.multiple',
            ])
            ->add('min', NumberType::class, [
                'label' => 'sylius.form.attribute_type_configuration.select.min'
            ])
            ->add('max', NumberType::class, [
                'label' => 'sylius.form.attribute_type_configuration.select.max'
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_attribute_type_configuration_select';
    }
}
