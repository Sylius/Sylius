<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/checkers.php');
    $container->import('services/form.php');
    $container->import('services/listeners.php');


    $services->set('sylius.custom_factory.zone', 'Sylius\Component\Addressing\Factory\ZoneFactory')
        ->decorate('sylius.factory.zone', null, 256)
        ->args([
            service('sylius.custom_factory.zone.inner'),
            service('sylius.factory.zone_member'),
        ]);

    $services->alias('Sylius\Component\Addressing\Factory\ZoneFactoryInterface', 'sylius.custom_factory.zone');

    $services->set('sylius.provider.province_naming', 'Sylius\Component\Addressing\Provider\ProvinceNamingProvider')
        ->lazy()
        ->args([service('sylius.repository.province')]);

    $services->alias('Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface', 'sylius.provider.province_naming');

    $services->set('sylius.matcher.zone', 'Sylius\Component\Addressing\Matcher\ZoneMatcher')
        ->public()
        ->args([service('sylius.repository.zone')]);

    $services->alias('Sylius\Component\Addressing\Matcher\ZoneMatcherInterface', 'sylius.matcher.zone')
        ->public();

    $services->set('sylius.converter.country_name', 'Sylius\Component\Addressing\Converter\CountryNameConverter');

    $services->alias('Sylius\Component\Addressing\Converter\CountryNameConverterInterface', 'sylius.converter.country_name');

    $services->set('sylius.comparator.address', 'Sylius\Component\Addressing\Comparator\AddressComparator');

    $services->alias('Sylius\Component\Addressing\Comparator\AddressComparatorInterface', 'sylius.comparator.address');

    $services->set('sylius.twig.extension.country_name', 'Sylius\Bundle\AddressingBundle\Twig\CountryNameExtension')
        ->args([''])
        ->tag('twig.extension');

    $services->set('sylius.twig.extension.province_naming', 'Sylius\Bundle\AddressingBundle\Twig\ProvinceNamingExtension')
        ->args([
            service('sylius.provider.province_naming'),
            '',
        ])
        ->tag('twig.extension');

    $services->set('sylius.validator.valid_province_address', 'Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator')
        ->args([
            service('sylius.repository.country'),
            service('sylius.repository.province'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_province_address_validator']);

    $services->set('sylius.validator.zone_cannot_contain_itself', 'Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneCannotContainItselfValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_cannot_contain_itself_validator']);

    $services->set('sylius.validator.unique_province_collection', 'Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollectionValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_unique_province_collection_validator']);

    $services->set('sylius.validator.zone_member_group', 'Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroupValidator')
        ->args(['%sylius.addressing.zone_member.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_member_group']);
};
