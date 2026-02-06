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

use Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpiredValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChangedValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CheckoutCompletionValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentRequestActionEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderAddressRequirementValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PlacedOrderCartItemsImmutableValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExistsValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpiredValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserVerificationTokenEligibilityValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOptionValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmailValidator;
use Sylius\Bundle\ApiBundle\Validator\Constraints\UpdateCartEmailNotAllowedValidator;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGenerator;
use Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.validator.unique_shop_user_email', UniqueShopUserEmailValidator::class)
        ->args([
            service('sylius.canonicalizer'),
            service('sylius.repository.shop_user'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_unique_shop_user_email']);

    $services->set('sylius_api.validator.order_not_empty', OrderNotEmptyValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_not_empty']);

    $services->set('sylius_api.validator.order_product_eligibility', OrderProductEligibilityValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_product_eligibility']);

    $services->set('sylius_api.validator.order_item_availability', OrderItemAvailabilityValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_item_availability']);

    $services->set('sylius_api.validator.order_shipping_method_eligibility', OrderShippingMethodEligibilityValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.checker.shipping_method_eligibility'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_order_shipping_method_eligibility']);

    $services->set('sylius_api.validator.checkout_completion', CheckoutCompletionValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_abstraction.state_machine'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_checkout_completion']);

    $services->set('sylius_api.validator.chosen_shipping_method_eligibility', ChosenShippingMethodEligibilityValidator::class)
        ->args([
            service('sylius.repository.shipment'),
            service('sylius.repository.shipping_method'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_chosen_shipping_method_eligibility']);

    $services->set('sylius_api.validator.adding_eligible_product_variant_to_cart', AddingEligibleProductVariantToCartValidator::class)
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_adding_eligible_product_variant_to_cart']);

    $services->set('sylius_api.validator.changed_item_quantity_in_cart', ChangedItemQuantityInCartValidator::class)
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.repository.order'),
            service('sylius.checker.inventory.availability'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_changed_item_quantity_in_cart']);

    $services->set('sylius_api.validator.correct_order_address', CorrectOrderAddressValidator::class)
        ->args([service('sylius.repository.country')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_correct_order_address']);

    $services->set('sylius_api.validator.order_payment_method_eligibility', OrderPaymentMethodEligibilityValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_order_payment_method_eligibility']);

    $services->set('sylius_api.validator.chosen_payment_method_eligibility', ChosenPaymentMethodEligibilityValidator::class)
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.payment_method'),
            service('sylius.resolver.payment_methods'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_chosen_payment_method_eligibility']);

    $services->set('sylius_api.validator.can_payment_method_be_changed', CanPaymentMethodBeChangedValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_can_payment_method_be_changed']);

    $services->set('sylius_api.validator.correct_change_shop_user_confirm_password', CorrectChangeShopUserConfirmPasswordValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_correct_change_shop_user_confirm_password']);

    $services->set('sylius_api.validator.confirm_reset_password', ConfirmResetPasswordValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_confirm_reset_password']);

    $services->set('sylius_api.validator.promotion_coupon_eligibility', PromotionCouponEligibilityValidator::class)
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('sylius.repository.order'),
            service('sylius_api.checker.applied_coupon_eligibility'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_promotion_coupon_eligibility']);

    $services->set('sylius_api.validator.shipment_already_shipped', ShipmentAlreadyShippedValidator::class)
        ->args([service('sylius.repository.shipment')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shipment_already_shipped']);

    $services->set('sylius_api.validator.shop_user_reset_password_token_exists', ShopUserResetPasswordTokenExistsValidator::class)
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_reset_password_token_exists']);

    $services->set('sylius_api.validator.shop_user_reset_password_token_not_expired', ShopUserResetPasswordTokenNotExpiredValidator::class)
        ->args([
            service('sylius.repository.shop_user'),
            '%sylius.shop_user.token.password_reset.ttl%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_reset_password_token_not_expired']);

    $services->set('sylius_api.validator.shop_user_not_verified', ShopUserNotVerifiedValidator::class)
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_shop_user_not_verified']);

    $services->set('sylius_api.validator.shop_user_verification_token_eligibility', ShopUserVerificationTokenEligibilityValidator::class)
        ->args([service('sylius.repository.shop_user')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_shop_user_verification_token_eligibility']);

    $services->set('sylius_api.validator.single_value_for_product_variant_option', SingleValueForProductVariantOptionValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_single_value_for_product_variant_option']);

    $services->set('sylius_api.validator.unique_reviewer_email', UniqueReviewerEmailValidator::class)
        ->args([
            service('sylius.repository.shop_user'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_unique_reviewer_email_validator']);

    $services->set('sylius_api.validator.admin_reset_password_token_non_expired', AdminResetPasswordTokenNonExpiredValidator::class)
        ->args([
            service('sylius.repository.admin_user'),
            '%sylius.admin_user.token.password_reset.ttl%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_validator_admin_non_expired_password_reset_token']);

    $services->set('sylius_api.validator.chosen_payment_request_action_eligibility', ChosenPaymentRequestActionEligibilityValidator::class)
        ->args([
            service('sylius.repository.payment_method'),
            service('sylius.command_provider.payment_request.default'),
            service('sylius.provider.payment_request.gateway_factory_name'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_api_chosen_payment_request_action_eligibility']);

    $services->set('sylius_api.validator.order_address_requirement', OrderAddressRequirementValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_address_requirement_validator']);

    $services->set('sylius_api.validator.groups_generator.shipping_method_configuration', ShippingMethodConfigurationGroupsGenerator::class)
        ->decorate('sylius.validator.groups_generator.shipping_method_configuration')
        ->args(['%sylius.shipping.shipping_method_calculator.validation_groups%'])
        ->tag('api_platform.validation_groups_generator');

    $services->set('sylius_api.validator.groups_generator.payment_method', PaymentMethodGroupsGenerator::class)
        ->decorate('sylius.validator.groups_generator.payment_method')
        ->args([
            '%sylius.form.type.payment_method.validation_groups%',
            service('sylius.validator.groups_generator.gateway_config'),
        ])
        ->tag('api_platform.validation_groups_generator');

    $services->set('sylius_api.validator.update_cart_email_not_allowed', UpdateCartEmailNotAllowedValidator::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_update_cart_email_not_allowed']);

    $services->set('sylius_api.validator.placed_order_cart_items_immutable', PlacedOrderCartItemsImmutableValidator::class)
        ->args([service('sylius.repository.order')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_validator_placed_order_cart_items_immutable']);
};
