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

use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssigner;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChanger;
use Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityChecker;
use Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverter;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Sylius\Bundle\ApiBundle\EventListener\AdminAuthenticationSuccessListener;
use Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener;
use Sylius\Bundle\ApiBundle\EventListener\AuthenticationSuccessListener;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapper;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifier;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor;
use Sylius\Component\Addressing\Model\AddressInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

return static function (ContainerConfigurator $container) {
    $container->import('services/**/*.php');

    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.model.address.interface', AddressInterface::class);

    $services
        ->set('sylius_api.extractor.property_info.empty_property_list', EmptyPropertyListExtractor::class)
        ->tag('property_info.list_extractor', ['priority' => -2000])
    ;

    $services
        ->set('sylius_api.changer.payment_method', PaymentMethodChanger::class)
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.payment_method'),
        ])
    ;
    $services->alias(PaymentMethodChangerInterface::class, 'sylius_api.changer.payment_method');

    $services
        ->set('sylius_api.listener.api_cart_blamer', ApiCartBlamerListener::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.command_bus'),
        ])
        ->tag('kernel.event_listener', ['event' => LoginSuccessEvent::class, 'method' => 'onLoginSuccess'])
    ;

    $services
        ->set('sylius_api.listener.authentication_success', AuthenticationSuccessListener::class)
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('kernel.event_listener', ['event' => 'lexik_jwt_authentication.on_authentication_success', 'method' => 'onAuthenticationSuccessResponse'])
    ;

    $services
        ->set('sylius_api.converter.iri_to_identifier', IriToIdentifierConverter::class)
        ->args([
            service('api_platform.router'),
            service('api_platform.metadata.resource.metadata_collection_factory'),
            service('api_platform.uri_variables.converter'),
        ])
    ;
    $services->alias(IriToIdentifierConverterInterface::class, 'sylius_api.converter.iri_to_identifier');

    $services->set('sylius_api.mapper.address', AddressMapper::class)->public();
    $services->alias(AddressMapperInterface::class, 'sylius_api.mapper.address');

    $services
        ->set('sylius_api.checker.applied_coupon_eligibility', AppliedCouponEligibilityChecker::class)
        ->args([
            service('sylius.checker.promotion_eligibility'),
            service('sylius.checker.promotion_coupon_eligibility'),
        ])
    ;
    $services->alias(AppliedCouponEligibilityCheckerInterface::class, 'sylius_api.checker.applied_coupon_eligibility');

    $services
        ->set('sylius_api.modifier.order_address', OrderAddressModifier::class)
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius_api.mapper.address'),
            service('sylius.resolver.customer'),
        ])
        ->public()
    ;
    $services->alias(OrderAddressModifierInterface::class, 'sylius_api.modifier.order_address');

    $services
        ->set('sylius_api.assigner.order_promotion_code', OrderPromotionCodeAssigner::class)
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('sylius.order_processing.order_processor'),
        ])
        ->public()
    ;
    $services->alias(OrderPromotionCodeAssignerInterface::class, 'sylius_api.assigner.order_promotion_code');

    $services
        ->set('sylius_api.listener.admin_authentication_success', AdminAuthenticationSuccessListener::class)
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('kernel.event_listener', ['event' => 'lexik_jwt_authentication.on_authentication_success', 'method' => 'onAuthenticationSuccessResponse'])
    ;
};
