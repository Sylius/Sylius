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

use Sylius\Bundle\ApiBundle\Attribute\ChannelCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LocaleCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\LoggedInCustomerEmailAware;
use Sylius\Bundle\ApiBundle\Attribute\OrderItemIdAware;
use Sylius\Bundle\ApiBundle\Attribute\OrderTokenValueAware;
use Sylius\Bundle\ApiBundle\Attribute\PaymentIdAware;
use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestActionAware;
use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestHashAware;
use Sylius\Bundle\ApiBundle\Attribute\PromotionCodeAware;
use Sylius\Bundle\ApiBundle\Attribute\ShipmentIdAware;
use Sylius\Bundle\ApiBundle\Attribute\ShopUserIdAware;
use Sylius\Bundle\ApiBundle\Attribute\TokenAware;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser;
use Sylius\Bundle\ApiBundle\Command\Admin\Account\ResetPassword as AdminResetPassword;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\ChannelCodeAwareContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\ChannelContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\HttpRequestMethodTypeContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleCodeAwareContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LocaleContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInCustomerEmailAwareContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\LoggedInShopUserIdAwareContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\PaymentRequestActionAwareContextBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.context_builder.channel_code_aware', ChannelCodeAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            ChannelCodeAware::class,
            ChannelCodeAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.context.channel'),
        ]);

    $services->set('sylius_api.context_builder.logged_in_customer_email_aware', LoggedInCustomerEmailAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            LoggedInCustomerEmailAware::class,
            LoggedInCustomerEmailAware::DEFAULT_ARGUMENT_NAME,
            service('sylius_api.context.user.token_based'),
        ]);

    $services->set('sylius_api.context_builder.channel', ChannelContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            service('sylius.context.channel'),
        ]);

    $services->set('sylius_api.context_builder.locale_code_aware', LocaleCodeAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            LocaleCodeAware::class,
            LocaleCodeAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.context.locale'),
        ]);

    $services->set('sylius_api.context_builder.locale', LocaleContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            service('sylius.context.locale'),
        ]);

    $services->set('sylius_api.context_builder.http_request_method_type', HttpRequestMethodTypeContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([service('.inner')]);

    $services->set('sylius_api.context_builder.logged_in_shop_user_id_aware', LoggedInShopUserIdAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            ShopUserIdAware::class,
            ShopUserIdAware::DEFAULT_ARGUMENT_NAME,
            service('sylius_api.context.user.token_based'),
        ]);

    $services->set('sylius_api.context_builder.shipment_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            ShipmentIdAware::class,
            ShipmentIdAware::DEFAULT_ARGUMENT_NAME,
            ShipmentInterface::class,
        ]);

    $services->set('sylius_api.context_builder.payment_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            PaymentIdAware::class,
            PaymentIdAware::DEFAULT_ARGUMENT_NAME,
            PaymentInterface::class,
        ]);

    $services->set('sylius_api.context_builder.payment_request_hash_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            PaymentRequestHashAware::class,
            PaymentRequestHashAware::DEFAULT_ARGUMENT_NAME,
            PaymentRequestInterface::class,
        ]);

    $services->set('sylius_api.context_builder.order_token_value_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            OrderTokenValueAware::class,
            OrderTokenValueAware::DEFAULT_ARGUMENT_NAME,
            OrderInterface::class,
        ]);

    $services->set('sylius_api.context_builder.order_item_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            OrderItemIdAware::class,
            OrderItemIdAware::DEFAULT_ARGUMENT_NAME,
            OrderItemInterface::class,
        ]);

    $services->set('sylius_api.context_builder.promotion_code_aware', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            PromotionCodeAware::class,
            PromotionCodeAware::DEFAULT_ARGUMENT_NAME,
            PromotionInterface::class,
        ]);

    $services->set('sylius_api.context_builder.token_aware.admin_user_reset_password', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            TokenAware::class,
            TokenAware::DEFAULT_ARGUMENT_NAME,
            AdminResetPassword::class,
        ]);

    $services->set('sylius_api.context_builder.token_aware.shop_user_reset_password', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            TokenAware::class,
            TokenAware::DEFAULT_ARGUMENT_NAME,
            ResetPassword::class,
        ]);

    $services->set('sylius_api.context_builder.token_aware.verify_shop_user', UriVariablesAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('.inner'),
            TokenAware::class,
            TokenAware::DEFAULT_ARGUMENT_NAME,
            VerifyShopUser::class,
        ]);

    $services->set('sylius_api.context_builder.payment_request_action_aware', PaymentRequestActionAwareContextBuilder::class)
        ->decorate('api_platform.serializer.context_builder', null, 64)
        ->args([
            service('sylius_api.converter.iri_to_identifier'),
            service('.inner'),
            PaymentRequestActionAware::class,
            PaymentRequestActionAware::DEFAULT_ARGUMENT_NAME,
            service('sylius.provider.payment_request.default_action'),
        ]);
};
