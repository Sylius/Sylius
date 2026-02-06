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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\CheckboxAttributeType as CheckboxAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DateAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DatetimeAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeValueTranslationsType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\TextAttributeConfigurationType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\DateAttributeType as DateAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\DatetimeAttributeType as DatetimeAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\FloatAttributeType as FloatAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\IntegerAttributeType as IntegerAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\PercentAttributeType as PercentAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType as SelectAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\TextareaAttributeType as TextareaAttributeFormType;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\TextAttributeType as TextAttributeFormType;
use Sylius\Component\Attribute\AttributeType\CheckboxAttributeType;
use Sylius\Component\Attribute\AttributeType\DateAttributeType;
use Sylius\Component\Attribute\AttributeType\DatetimeAttributeType;
use Sylius\Component\Attribute\AttributeType\FloatAttributeType;
use Sylius\Component\Attribute\AttributeType\IntegerAttributeType;
use Sylius\Component\Attribute\AttributeType\PercentAttributeType;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\AttributeType\TextareaAttributeType;
use Sylius\Component\Attribute\AttributeType\TextAttributeType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.attribute_type.text', TextAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'text',
            'label' => 'Text',
            'form_type' => TextAttributeFormType::class,
            'configuration_form_type' => TextAttributeConfigurationType::class,
        ]);

    $services->set('sylius.attribute_type.textarea', TextareaAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'textarea',
            'label' => 'Textarea',
            'form_type' => TextareaAttributeFormType::class,
        ]);

    $services->set('sylius.attribute_type.checkbox', CheckboxAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'checkbox',
            'label' => 'Checkbox',
            'form_type' => CheckboxAttributeFormType::class,
        ]);

    $services->set('sylius.attribute_type.integer', IntegerAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'integer',
            'label' => 'Integer',
            'form_type' => IntegerAttributeFormType::class,
        ]);

    $services->set('sylius.attribute_type.float', FloatAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'float',
            'label' => 'Float',
            'form_type' => FloatAttributeFormType::class,
        ]);

    $services->set('sylius.attribute_type.percent', PercentAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'percent',
            'label' => 'Percent',
            'form_type' => PercentAttributeFormType::class,
        ]);

    $services->set('sylius.attribute_type.datetime', DatetimeAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'datetime',
            'label' => 'Datetime',
            'form_type' => DatetimeAttributeFormType::class,
            'configuration_form_type' => DatetimeAttributeConfigurationType::class,
        ]);

    $services->set('sylius.attribute_type.date', DateAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'date',
            'label' => 'Date',
            'form_type' => DateAttributeFormType::class,
            'configuration_form_type' => DateAttributeConfigurationType::class,
        ]);

    $services->set('sylius.attribute_type.select', SelectAttributeType::class)
        ->tag('sylius.attribute.type', [
            'attribute_type' => 'select',
            'label' => 'Select',
            'form_type' => SelectAttributeFormType::class,
            'configuration_form_type' => SelectAttributeConfigurationType::class,
        ]);

    $services->set('sylius.form.type.attribute_type.select', SelectAttributeFormType::class)
        ->args([service('sylius.translation_locale_provider')])
        ->tag('form.type');

    $services->set('sylius.form.type.attribute_type.configuration.select_attribute_choices_collection', SelectAttributeChoicesCollectionType::class)
        ->tag('form.type');

    $services->set('sylius.form.type.attribute_type.configuration.select_attribute_value_translations', SelectAttributeValueTranslationsType::class)
        ->args([service('sylius.translation_locale_provider')])
        ->tag('form.type');
};
