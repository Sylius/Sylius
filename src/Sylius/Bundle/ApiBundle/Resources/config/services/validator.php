<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.validator.unique_shop_user_email', 'Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmailValidator')
        ->args([
            service('sylius.canonicalizer'),
            service('sylius.repository.shop_user'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_unique_shop_user_email']);

    $services->set('sylius_api.validator.order_not_empty', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_not_empty']);

    $services->set('sylius_api.validator.order_product_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_product_eligibility']);

    $services->set('sylius_api.validator.order_item_availability', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator')
        ->args([
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_item_availability']);

    $services->set('sylius_api.validator.order_shipping_method_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator')
        ->args([
            service('sylius.repository.order'),
            service('sylius.checker.shipping_method_eligibility'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_shipping_method_eligibility']);

    $services->set('sylius_api.validator.checkout_completion', 'Sylius\Bundle\ApiBundle\Validator\Constraints\CheckoutCompletionValidator')
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_checkout_completion']);

    $services->set('sylius_api.validator.chosen_shipping_method_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator')
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.repository.shipping_method'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_chosen_shipping_method_eligibility']);

    $services->set('sylius_api.validator.adding_eligible_product_variant_to_cart', 'Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator')
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_adding_eligible_product_variant_to_cart']);

    $services->set('sylius_api.validator.changed_item_quantity_in_cart', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator')
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_changed_item_quantity_in_cart']);

    $services->set('sylius_api.validator.correct_order_address', 'Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator')
        ->args([service('sylius.repository.country')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_correct_order_address']);

    $services->set('sylius_api.validator.order_payment_method_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_order_payment_method_eligibility']);

    $services->set('sylius_api.validator.chosen_payment_method_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator')
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.payment_method'),
            service('sylius.resolver.payment_methods'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_chosen_payment_method_eligibility']);

    $services->set('sylius_api.validator.can_payment_method_be_changed', 'Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChangedValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_can_payment_method_be_changed']);

    $services->set('sylius_api.validator.correct_change_shop_user_confirm_password', 'Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_correct_change_shop_user_confirm_password']);

    $services->set('sylius_api.validator.confirm_reset_password', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_confirm_reset_password']);

    $services->set('sylius_api.validator.promotion_coupon_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator')
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('sylius.repository.order'),
            service('sylius_api.checker.applied_coupon_eligibility'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_promotion_coupon_eligibility']);

    $services->set('sylius_api.validator.shipment_already_shipped', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator')
        ->args([service('sylius.repository.shipment')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shipment_already_shipped']);

    $services->set('sylius_api.validator.shop_user_reset_password_token_exists', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExistsValidator')
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_reset_password_token_exists']);

    $services->set('sylius_api.validator.shop_user_reset_password_token_not_expired', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpiredValidator')
        ->args([
            service('sylius.repository.shop_user'),
            '%sylius.shop_user.token.password_reset.ttl%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_reset_password_token_not_expired']);

    $services->set('sylius_api.validator.shop_user_not_verified', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator')
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_not_verified']);

    $services->set('sylius_api.validator.shop_user_verification_token_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserVerificationTokenEligibilityValidator')
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_shop_user_verification_token_eligibility']);

    $services->set('sylius_api.validator.single_value_for_product_variant_option', 'Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOptionValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_single_value_for_product_variant_option']);

    $services->set('sylius_api.validator.unique_reviewer_email', 'Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator')
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_unique_reviewer_email_validator']);

    $services->set('sylius_api.validator.admin_reset_password_token_non_expired', 'Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpiredValidator')
        ->args([
            service('sylius.repository.admin_user'),
            '%sylius.admin_user.token.password_reset.ttl%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_admin_non_expired_password_reset_token']);

    $services->set('sylius_api.validator.chosen_payment_request_action_eligibility', 'Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentRequestActionEligibilityValidator')
        ->args([
            service('sylius.repository.payment_method'),
            service('sylius.command_provider.payment_request.default'),
            service('sylius.provider.payment_request.gateway_factory_name'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_chosen_payment_request_action_eligibility']);

    $services->set('sylius_api.validator.order_address_requirement', 'Sylius\Bundle\ApiBundle\Validator\Constraints\OrderAddressRequirementValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_address_requirement_validator']);

    $services->set('sylius_api.validator.groups_generator.shipping_method_configuration', 'Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator')
        ->decorate('sylius.validator.groups_generator.shipping_method_configuration')
        ->args(['%sylius.shipping.shipping_method_calculator.validation_groups%'])
        ->tag('api_platform.validation_groups_generator');

    $services->set('sylius_api.validator.groups_generator.payment_method', 'Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGenerator')
        ->decorate('sylius.validator.groups_generator.payment_method')
        ->args([
            '%sylius.form.type.payment_method.validation_groups%',
            service('sylius.validator.groups_generator.gateway_config'),
        ])
        ->tag('api_platform.validation_groups_generator');

    $services->set('sylius_api.validator.update_cart_email_not_allowed', 'Sylius\Bundle\ApiBundle\Validator\Constraints\UpdateCartEmailNotAllowedValidator')
        ->args([
            service('sylius.repository.order'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_update_cart_email_not_allowed']);

    $services->set('sylius_api.validator.placed_order_cart_items_immutable', 'Sylius\Bundle\ApiBundle\Validator\Constraints\PlacedOrderCartItemsImmutableValidator')
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_placed_order_cart_items_immutable']);
};
