<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/attribute_types.php');
    
    $parameters->set('sylius.model.attribute.interface', 'Sylius\Component\Attribute\AttributeType\AttributeTypeInterface');

    $services->set('sylius.doctrine.orm.event_subscriber.load_metadata.attribute', 'Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber')
        ->public()
        ->args(['%sylius.attribute.subjects%'])
        ->tag('doctrine.event_subscriber');

    $services->set('sylius.registry.attribute_type', 'Sylius\Component\Registry\ServiceRegistry')
        ->args([
            '%sylius.model.attribute.interface%',
            'attribute type',
        ]);

    $services->set('sylius.form_registry.attribute_type', 'Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry');

    $services->set('sylius.form.type.attribute_type_choice', 'Sylius\Bundle\AttributeBundle\Form\Type\AttributeTypeChoiceType')
        ->args(['%sylius.attribute.attribute_types%'])
        ->tag('form.type');

    $services->set('sylius.validator.attribute_type', 'Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeTypeValidator')
        ->args([service('sylius.registry.attribute_type')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_attribute_type_validator']);

    $services->set('sylius.validator.valid_attribute_value', 'Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValueValidator')
        ->args([service('sylius.registry.attribute_type')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_attribute_value_validator']);

    $services->set('sylius.validator.valid_text_attribute_configuration', 'Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfigurationValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_text_attribute_validator']);

    $services->set('sylius.validator.valid_select_attribute_configuration', 'Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfigurationValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_select_attribute_validator']);
};
