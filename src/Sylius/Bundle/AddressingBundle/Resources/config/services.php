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

use Sylius\Bundle\AddressingBundle\Twig\CountryNameExtension;
use Sylius\Bundle\AddressingBundle\Twig\ProvinceNamingExtension;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraintValidator;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\UniqueProvinceCollectionValidator;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneCannotContainItselfValidator;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroupValidator;
use Sylius\Component\Addressing\Comparator\AddressComparator;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Addressing\Converter\CountryNameConverter;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Addressing\Factory\ZoneFactory;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcher;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Provider\ProvinceNamingProvider;
use Sylius\Component\Addressing\Provider\ProvinceNamingProviderInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/*.php');

    $services = $container->services();

    $services
        ->set('sylius.custom_factory.zone', ZoneFactory::class)
        ->decorate('sylius.factory.zone', null, 256)
        ->args([
            service('sylius.custom_factory.zone.inner'),
            service('sylius.factory.zone_member'),
        ])
    ;
    $services->alias(ZoneFactoryInterface::class, 'sylius.custom_factory.zone');

    $services
        ->set('sylius.provider.province_naming', ProvinceNamingProvider::class)
        ->args([service('sylius.repository.province')])
        ->lazy()
    ;
    $services->alias(ProvinceNamingProviderInterface::class, 'sylius.provider.province_naming');

    $services
        ->set('sylius.matcher.zone', ZoneMatcher::class)
        ->args([service('sylius.repository.zone')])
        ->public()
    ;
    $services->alias(ZoneMatcherInterface::class, 'sylius.matcher.zone')->public();

    $services->set('sylius.converter.country_name', CountryNameConverter::class);
    $services->alias(CountryNameConverterInterface::class, 'sylius.converter.country_name');

    $services->set('sylius.comparator.address', AddressComparator::class);
    $services->alias(AddressComparatorInterface::class, 'sylius.comparator.address');

    $services->set('sylius.twig.extension.country_name', CountryNameExtension::class)->tag('twig.extension');

    $services
        ->set('sylius.twig.extension.province_naming', ProvinceNamingExtension::class)
        ->args([service('sylius.provider.province_naming')])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius.validator.valid_province_address', ProvinceAddressConstraintValidator::class)
        ->args([
            service('sylius.repository.country'),
            service('sylius.repository.province'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_province_address_validator'])
    ;

    $services
        ->set('sylius.validator.zone_cannot_contain_itself', ZoneCannotContainItselfValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_cannot_contain_itself_validator'])
    ;

    $services
        ->set('sylius.validator.unique_province_collection', UniqueProvinceCollectionValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_unique_province_collection_validator'])
    ;

    $services
        ->set('sylius.validator.zone_member_group', ZoneMemberGroupValidator::class)
        ->args(['%sylius.addressing.zone_member.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_member_group'])
    ;
};
