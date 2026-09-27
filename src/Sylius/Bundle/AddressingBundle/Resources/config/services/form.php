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

use Sylius\Bundle\AddressingBundle\Form\EventListener\BuildAddressFormSubscriber;
use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryCodeChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\CountryType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceCodeChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ProvinceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneCodeChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneMemberType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.address.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.country.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.province.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.zone.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.zone_member.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.address', AddressType::class)
        ->args([
            '%sylius.model.address.class%',
            '%sylius.form.type.address.validation_groups%',
            inline_service(BuildAddressFormSubscriber::class)
                ->args([
                    service('sylius.repository.country'),
                    service('form.factory'),
                ]),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.country', CountryType::class)
        ->args([
            '%sylius.model.country.class%',
            '%sylius.form.type.country.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.country_choice', CountryChoiceType::class)
        ->args([service('sylius.repository.country')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.country_code_choice', CountryCodeChoiceType::class)
        ->args([service('sylius.repository.country')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.province', ProvinceType::class)
        ->args([
            '%sylius.model.province.class%',
            '%sylius.form.type.province.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.province_choice', ProvinceChoiceType::class)
        ->args([service('sylius.repository.province')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.province_code_choice', ProvinceCodeChoiceType::class)
        ->args([service('sylius.repository.province')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.zone', ZoneType::class)
        ->args([
            '%sylius.model.zone.class%',
            '%sylius.form.type.zone.validation_groups%',
            '%sylius.scope.zone%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.zone_choice', ZoneChoiceType::class)
        ->args([
            service('sylius.repository.zone'),
            '%sylius.scope.zone%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.zone_code_choice', ZoneCodeChoiceType::class)
        ->args([service('sylius.repository.zone')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.zone_member', ZoneMemberType::class)
        ->args([
            '%sylius.model.zone_member.class%',
            '%sylius.form.type.zone_member.validation_groups%',
        ])
        ->tag('form.type')
    ;
};
