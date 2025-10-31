<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.attribute_type.text', 'Sylius\Component\Attribute\AttributeType\TextAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'text', 'label' => 'Text', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\TextAttributeType', 'configuration_form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\TextAttributeConfigurationType']);

    $services->set('sylius.attribute_type.textarea', 'Sylius\Component\Attribute\AttributeType\TextareaAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'textarea', 'label' => 'Textarea', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\TextareaAttributeType']);

    $services->set('sylius.attribute_type.checkbox', 'Sylius\Component\Attribute\AttributeType\CheckboxAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'checkbox', 'label' => 'Checkbox', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\CheckboxAttributeType']);

    $services->set('sylius.attribute_type.integer', 'Sylius\Component\Attribute\AttributeType\IntegerAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'integer', 'label' => 'Integer', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\IntegerAttributeType']);

    $services->set('sylius.attribute_type.float', 'Sylius\Component\Attribute\AttributeType\FloatAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'float', 'label' => 'Float', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\FloatAttributeType']);

    $services->set('sylius.attribute_type.percent', 'Sylius\Component\Attribute\AttributeType\PercentAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'percent', 'label' => 'Percent', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\PercentAttributeType']);

    $services->set('sylius.attribute_type.datetime', 'Sylius\Component\Attribute\AttributeType\DatetimeAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'datetime', 'label' => \Datetime::class, 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\DatetimeAttributeType', 'configuration_form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DatetimeAttributeConfigurationType']);

    $services->set('sylius.attribute_type.date', 'Sylius\Component\Attribute\AttributeType\DateAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'date', 'label' => 'Date', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\DateAttributeType', 'configuration_form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\DateAttributeConfigurationType']);

    $services->set('sylius.attribute_type.select', 'Sylius\Component\Attribute\AttributeType\SelectAttributeType')
        ->tag('sylius.attribute.type', ['attribute_type' => 'select', 'label' => 'Select', 'form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType', 'configuration_form_type' => 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeConfigurationType']);

    $services->set('sylius.form.type.attribute_type.select', 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\SelectAttributeType')
        ->args([service('sylius.translation_locale_provider')])
        ->tag('form.type');

    $services->set('sylius.form.type.attribute_type.configuration.select_attribute_choices_collection', 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType')
        ->tag('form.type');

    $services->set('sylius.form.type.attribute_type.configuration.select_attribute_value_translations', 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeValueTranslationsType')
        ->args([service('sylius.translation_locale_provider')])
        ->tag('form.type');
};
