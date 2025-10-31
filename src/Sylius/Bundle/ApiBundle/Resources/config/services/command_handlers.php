<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.command_handler.account.register_shop_user', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\RegisterShopUserHandler')
        ->args([
            service('sylius.factory.shop_user'),
            service('sylius.manager.shop_user'),
            service('sylius.resolver.customer'),
            service('sylius.repository.channel'),
            service('sylius.shop_user.token_generator.email_verification'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.pickup_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler')
        ->args([
            service('sylius.factory.order'),
            service('sylius.repository.order'),
            service('sylius.repository.channel'),
            service('sylius.manager.order'),
            service('sylius.random_generator'),
            service('sylius.repository.customer'),
            '%sylius_core.order_token_length%',
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.add_item_to_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\AddItemToCartHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.product_variant'),
            service('sylius.modifier.order'),
            service('sylius.factory.order_item'),
            service('sylius.modifier.order_item_quantity'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.remove_item_from_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\RemoveItemFromCartHandler')
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.modifier.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.inform_about_cart_recalculation', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\InformAboutCartRecalculationHandler')
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.update_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius_api.modifier.order_address'),
            service('sylius_api.assigner.order_promotion_code'),
            service('sylius.resolver.customer'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.choose_shipping_method', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChooseShippingMethodHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.shipping_method'),
            service('sylius.repository.shipment'),
            service('sylius.checker.shipping_method_eligibility'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.choose_payment_method', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChoosePaymentMethodHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.payment_method'),
            service('sylius.repository.payment'),
            service('sylius_abstraction.state_machine'),
            service('sylius_api.changer.payment_method'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.complete_order', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\CompleteOrderHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
            service('sylius.command_bus'),
            service('sylius.event_bus'),
            service('sylius.checker.order.promotions_integrity'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.ship_shipment', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ShipShipmentHandler')
        ->args([
            service('sylius.repository.shipment'),
            service('sylius_abstraction.state_machine'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.change_payment_method', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangePaymentMethodHandler')
        ->args([
            service('sylius_api.changer.payment_method'),
            service('sylius.repository.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.change_item_quantity_in_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\ChangeItemQuantityInCartHandler')
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.modifier.order_item_quantity'),
            service('sylius.order_processing.order_processor'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.catalog.add_product_review', 'Sylius\Bundle\ApiBundle\CommandHandler\Catalog\AddProductReviewHandler')
        ->args([
            service('sylius.factory.product_review'),
            service('sylius.repository.product_review'),
            service('sylius.repository.product'),
            service('sylius.resolver.customer'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.cart.blame_cart', 'Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.repository.order'),
            service('sylius.order_processing.order_processor'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.change_shop_user_password', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler')
        ->args([
            service('sylius.security.password_updater'),
            service('sylius.repository.shop_user'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.request_reset_password_token', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('messenger.default_bus'),
            service('sylius.shop_user.token_generator.password_reset'),
            service('clock'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.request_shop_user_verification', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.shop_user.token_generator.email_verification'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.reset_password', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler')
        ->args([service('sylius.resetter.user_password.shop')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.send_account_registration_email', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.repository.channel'),
            service('sylius.mailer.account_registration_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.send_shop_user_verification_email', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius.repository.channel'),
            service('sylius.mailer.account_verification_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.send_order_confirmation', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler')
        ->args([
            service('sylius.repository.order'),
            service('sylius.mailer.order_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.send_reset_password_email', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.repository.shop_user'),
            service('sylius.mailer.reset_password_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.checkout.send_shipment_confirmation_email', 'Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler')
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.mailer.shipment_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.account.verify_shop_user', 'Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler')
        ->args([
            service('sylius.repository.shop_user'),
            service('clock'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.send_contract_request', 'Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.mailer.contact_email_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.promotion.generate_promotion_coupon', 'Sylius\Bundle\ApiBundle\CommandHandler\Promotion\GeneratePromotionCouponHandler')
        ->args([
            service('sylius.repository.promotion'),
            service('sylius.generator.promotion_coupon'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.customer.remove_shop_user', 'Sylius\Bundle\ApiBundle\CommandHandler\Customer\RemoveShopUserHandler')
        ->args([service('sylius.repository.shop_user')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.payment.add_payment_request', 'Sylius\Bundle\ApiBundle\CommandHandler\Payment\AddPaymentRequestHandler')
        ->args([
            service('sylius.repository.payment_method'),
            service('sylius.repository.payment'),
            service('sylius.factory.payment_request'),
            service('sylius.repository.payment_request'),
            service('sylius.provider.payment_request.default_action'),
            service('sylius.provider.payment_request.default_payload'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);

    $services->set('sylius_api.command_handler.payment.update_payment_request', 'Sylius\Bundle\ApiBundle\CommandHandler\Payment\UpdatePaymentRequestHandler')
        ->args([service('sylius.repository.payment_request')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
