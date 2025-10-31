<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.context_builder.channel_code_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\ChannelCodeAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware',
            \Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.context.channel'),
        ]);

    $services->set('sylius_api.context_builder.logged_in_customer_email_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInCustomerEmailAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware',
            \Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware::DEFAULT_ARGUMENT_NAME,
            service('sylius_api.context.user.token_based'),
        ]);

    $services->set('sylius_api.context_builder.channel', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\ChannelContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            service('sylius.context.channel'),
        ]);

    $services->set('sylius_api.context_builder.locale_code_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleCodeAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware',
            \Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.context.locale'),
        ]);

    $services->set('sylius_api.context_builder.locale', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            service('sylius.context.locale'),
        ]);

    $services->set('sylius_api.context_builder.http_request_method_type', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\HttpRequestMethodTypeContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([service('.inner')]);

    $services->set('sylius_api.context_builder.logged_in_shop_user_id_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInShopUserIdAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware',
            \Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware::DEFAULT_ARGUMENT_NAME,
            service('sylius_api.context.user.token_based'),
        ]);

    $services->set('sylius_api.context_builder.shipment_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware',
            \Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Core\Model\ShipmentInterface',
        ]);

    $services->set('sylius_api.context_builder.payment_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\PaymentIdAware',
            \Sylius\Bundle\ApiBundle\Attribute\PaymentIdAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Core\Model\PaymentInterface',
        ]);

    $services->set('sylius_api.context_builder.payment_request_hash_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\PaymentRequestHashAware',
            \Sylius\Bundle\ApiBundle\Attribute\PaymentRequestHashAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Payment\Model\PaymentRequestInterface',
        ]);

    $services->set('sylius_api.context_builder.order_token_value_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware',
            \Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Core\Model\OrderInterface',
        ]);

    $services->set('sylius_api.context_builder.order_item_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\OrderItemIdAware',
            \Sylius\Bundle\ApiBundle\Attribute\OrderItemIdAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Core\Model\OrderItemInterface',
        ]);

    $services->set('sylius_api.context_builder.promotion_code_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\PromotionCodeAware',
            \Sylius\Bundle\ApiBundle\Attribute\PromotionCodeAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Component\Core\Model\PromotionInterface',
        ]);

    $services->set('sylius_api.context_builder.token_aware.admin_user_reset_password', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\TokenAware',
            \Sylius\Bundle\ApiBundle\Attribute\TokenAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Bundle\ApiBundle\Command\Admin\Account\ResetPassword',
        ]);

    $services->set('sylius_api.context_builder.token_aware.shop_user_reset_password', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\TokenAware',
            \Sylius\Bundle\ApiBundle\Attribute\TokenAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Bundle\ApiBundle\Command\Account\ResetPassword',
        ]);

    $services->set('sylius_api.context_builder.token_aware.verify_shop_user', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\TokenAware',
            \Sylius\Bundle\ApiBundle\Attribute\TokenAware::DEFAULT_ARGUMENT_NAME,
            'Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser',
        ]);

    $services->set('sylius_api.context_builder.payment_request_action_aware', 'Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\PaymentRequestActionAwareContextBuilder')
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('sylius_api.converter.iri_to_identifier'),
            service('.inner'),
            'Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware',
            \Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.provider.payment_request.default_action'),
        ]);
};
