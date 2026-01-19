<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type\AttributeType\Configuration;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType as BaseSelectAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeValueTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SelectAttributeConfigurationType extends BaseSelectAttributeConfigurationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('choices', SelectAttributeChoicesCollectionType::class, [
            'entry_type' => SelectAttributeValueTranslationsType::class,
            'label' => 'sylius.form.attribute_type_configuration.select.values',
            'allow_add' => true,
            'allow_delete' => true,
            'required' => false,
            'entry_options' => [
                'entry_type' => TextType::class,
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_attribute_type_configuration_select';
    }
}
