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

use Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber;
use Sylius\Bundle\AttributeBundle\Form\Type\AttributeTypeChoiceType;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\AttributeTypeValidator;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValueValidator;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidSelectAttributeConfigurationValidator;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidTextAttributeConfigurationValidator;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Registry\ServiceRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/attribute_types.php');

    $parameters->set('sylius.model.attribute.interface', AttributeTypeInterface::class);

    $services->set('sylius.doctrine.orm.event_subscriber.load_metadata.attribute', LoadMetadataSubscriber::class)
        ->public()
        ->args(['%sylius.attribute.subjects%'])
        ->tag('doctrine.event_subscriber');

    $services->set('sylius.registry.attribute_type', ServiceRegistry::class)
        ->args([
            '%sylius.model.attribute.interface%',
            'attribute type',
        ]);

    $services->set('sylius.form_registry.attribute_type', FormTypeRegistry::class);

    $services->set('sylius.form.type.attribute_type_choice', AttributeTypeChoiceType::class)
        ->args(['%sylius.attribute.attribute_types%'])
        ->tag('form.type');

    $services->set('sylius.validator.attribute_type', AttributeTypeValidator::class)
        ->args([service('sylius.registry.attribute_type')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_attribute_type_validator']);

    $services->set('sylius.validator.valid_attribute_value', ValidAttributeValueValidator::class)
        ->args([service('sylius.registry.attribute_type')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_attribute_value_validator']);

    $services->set('sylius.validator.valid_text_attribute_configuration', ValidTextAttributeConfigurationValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_text_attribute_validator']);

    $services->set('sylius.validator.valid_select_attribute_configuration', ValidSelectAttributeConfigurationValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_select_attribute_validator']);
};
