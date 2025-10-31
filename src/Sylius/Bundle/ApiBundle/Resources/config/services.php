<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $container->import('services/**/*.php');
    
    $parameters->set('sylius.model.address.interface', 'Sylius\Component\Addressing\Model\AddressInterface');

    $services->set('sylius_api.extractor.property_info.empty_property_list', 'Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor')
        ->tag('property_info.list_extractor', ['priority' => -2000]);

    $services->set('sylius_api.changer.payment_method', 'Sylius\Bundle\ApiBundle\Changer\PaymentMethodChanger')
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.payment_method'),
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface', 'sylius_api.changer.payment_method');

    $services->set('sylius_api.listener.api_cart_blamer', 'Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener')
        ->args([
            service('sylius.context.cart'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.command_bus'),
        ])
        ->tag('kernel.event_listener', ['event' => 'Symfony\Component\Security\Http\Event\LoginSuccessEvent', 'method' => 'onLoginSuccess']);

    $services->set('sylius_api.listener.authentication_success', 'Sylius\Bundle\ApiBundle\EventListener\AuthenticationSuccessListener')
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('kernel.event_listener', ['event' => 'lexik_jwt_authentication.on_authentication_success', 'method' => 'onAuthenticationSuccessResponse']);

    $services->set('sylius_api.converter.iri_to_identifier', 'Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverter')
        ->args([
            service('api_platform.router'),
            service('api_platform.metadata.resource.metadata_collection_factory'),
            service('api_platform.uri_variables.converter'),
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface', 'sylius_api.converter.iri_to_identifier');

    $services->set('sylius_api.mapper.address', 'Sylius\Bundle\ApiBundle\Mapper\AddressMapper')
        ->public();

    $services->alias('Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface', 'sylius_api.mapper.address');

    $services->set('sylius_api.checker.applied_coupon_eligibility', 'Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityChecker')
        ->args([
            service('sylius.checker.promotion_eligibility'),
            service('sylius.checker.promotion_coupon_eligibility'),
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface', 'sylius_api.checker.applied_coupon_eligibility');

    $services->set('sylius_api.modifier.order_address', 'Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifier')
        ->public()
        ->args([
            service('sylius_abstraction.state_machine'),
            service('sylius_api.mapper.address'),
            service('sylius.resolver.customer'),
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface', 'sylius_api.modifier.order_address');

    $services->set('sylius_api.assigner.order_promotion_code', 'Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssigner')
        ->public()
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('sylius.order_processing.order_processor'),
        ]);

    $services->alias('Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface', 'sylius_api.assigner.order_promotion_code');

    $services->set('sylius_api.listener.admin_authentication_success', 'Sylius\Bundle\ApiBundle\EventListener\AdminAuthenticationSuccessListener')
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('kernel.event_listener', ['event' => 'lexik_jwt_authentication.on_authentication_success', 'method' => 'onAuthenticationSuccessResponse']);
};
