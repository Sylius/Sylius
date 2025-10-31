<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.address.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.country.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.province.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.zone.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.zone_member.validation_groups', ['sylius']);

    $services->set('sylius.form.type.address', 'Sylius\Bundle\AddressingBundle\Form\Type\AddressType')
        ->args([
            '%sylius.model.address.class%',
            '%sylius.form.type.address.validation_groups%',
            inline_service('Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber')
                ->args([
                    service('sylius.repository.country'),
                    service('form.factory'),
                ]),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.country', 'Sylius\Bundle\AddressingBundle\Form\Type\CountryType')
        ->args([
            '%sylius.model.country.class%',
            '%sylius.form.type.country.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.country_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType')
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius.form.type.country_code_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType')
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius.form.type.province', 'Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType')
        ->args([
            '%sylius.model.province.class%',
            '%sylius.form.type.province.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.province_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\ProvinceChoiceType')
        ->args([service('sylius.repository.province')])
        ->tag('form.type');

    $services->set('sylius.form.type.province_code_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType')
        ->args([service('sylius.repository.province')])
        ->tag('form.type');

    $services->set('sylius.form.type.zone', 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneType')
        ->args([
            '%sylius.model.zone.class%',
            '%sylius.form.type.zone.validation_groups%',
            '%sylius.scope.zone%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.zone_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType')
        ->args([
            service('sylius.repository.zone'),
            '%sylius.scope.zone%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.zone_code_choice', 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneCodeChoiceType')
        ->args([service('sylius.repository.zone')])
        ->tag('form.type');

    $services->set('sylius.form.type.zone_member', 'Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType')
        ->args([
            '%sylius.model.zone_member.class%',
            '%sylius.form.type.zone_member.validation_groups%',
        ])
        ->tag('form.type');
};
