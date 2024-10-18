# UPGRADE FROM `1.14` TO `2.0`

## Configuration

* Messenger:
    * The `sylius_default.bus` and `sylius_event.bus` configuration options were removed. Use `sylius.command_bus` and
      `sylius.event_bus` for commands and events respectively.

* SyliusStateMachineAbstraction:
    * The `sylius_state_machine_abstraction.default_adapter` option has been changed from `winzou_state_machine` to `symfony_workflow`.

* Resource:
    * The configuration nodes for resource options `sylius_*.resources.**.options` (`sylius_addressing.resources.address.options` etc.)
      were removed from all resource definitions in all bundles.

* The `sylius_core.autoconfigure_with_attributes` and `sylius_order.autoconfigure_with_attributes` configuration parameters 
  have been removed. If you want to autoconfigure order processors and cart contexts, use corresponding attributes 
  instead of interfaces: 
  
  * `Sylius\Bundle\OrderBundle\Attribute\AsCartContext`
  * `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor`

* The `sylius_user.resources.{name}.user.resetting.pin` configuration parameter has been removed.
  The corresponding email `reset_password_pin` and `Sylius\Bundle\UserBundle\Controller\UserController::requestPasswordResetPinAction`
  method have been removed. The related services have been removed as well:

    * `sylius.{user_type}_user.pin_generator.password_reset`
    * `sylius.{user_type}_user.pin_uniqueness_checker.password_reset`

## Dependencies

* The following dependencies have been removed, install them in your application, if you still want to use Winzou State Machine:

    * `winzou/state-machine`
    * `winzou/state-machine-bundle`

* The `swiftmailer/swiftmailer` dependency has been removed. Use `symfony/mailer` instead.

## Codebase

* Doctrine MongoDB and PHPCR is no longer supported in ResourceBundle and GridBundle:

* The following classes were removed:

    * `Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\DataSource`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\Driver`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilder`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionBuilderInterface`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExpressionVisitor`
    * `Sylius\Bundle\GridBundle\Doctrine\PHPCRODM\ExtraComparison`
    * `Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrineODMDriver`
    * `Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Doctrine\DoctrinePHPCRDriver`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\TranslatableRepository`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\DocumentRepository`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\DefaultParentListener`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameFilterListener`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\EventListener\NameResolverListener`
    * `Sylius\Bundle\ResourceBundle\Doctrine\ODM\PHPCR\Form\Builder\DefaultFormBuilder`
    * `Sylius\Bundle\ResourceBundle\EventListener\ODMMappedSuperClassSubscriber`
    * `Sylius\Bundle\ResourceBundle\EventListener\ODMRepositoryClassSubscriber`
    * `Sylius\Bundle\ResourceBundle\EventListener\ODMTranslatableListener`

* The following services were removed:

    * `sylius.event_subscriber.odm_mapped_super_class`
    * `sylius.event_subscriber.odm_repository_class`
    * `sylius.grid_driver.doctrine.phpcrodm`
    * `sylius.listener.api_postgresql_driver_exception_listener`

* Aliases introduced in Sylius 1.14 have now become the primary service IDs in Sylius 2.0. The old service IDs have been removed, and all references must be updated accordingly:

| Old ID                                                                                                     | New ID                                                                               |
|------------------------------------------------------------------------------------------------------------|--------------------------------------------------------------------------------------|
| **AttributeBundle**                                                                                        |                                                                                      |
| `sylius.form.type.attribute_type.select.choices_collection`                                                | `sylius.form.type.attribute_type.configuration.select_attribute_choices_collection`  |
| `sylius.attribute_type.select.value.translations`                                                          | `sylius.form.type.attribute_type.configuration.select_attribute_value_translations`  |
| `sylius.validator.valid_text_attribute`                                                                    | `sylius.validator.valid_text_attribute_configuration`                                |
| `sylius.validator.valid_select_attribute`                                                                  | `sylius.validator.valid_select_attribute_configuration`                              |
| **AdminBundle**                                                                                            |                                                                                      |
| `sylius.security.shop_user_impersonator`                                                                   | `sylius_admin.security.shop_user_impersonator`                                       |
| `sylius.controller.impersonate_user`                                                                       | `sylius_admin.controller.impersonate_user`                                           |
| `Sylius\Bundle\AdminBundle\Action\Account\RenderResetPasswordPageAction`                                   | `sylius_admin.controller.account.render_reset_password_page`                         |
| `Sylius\Bundle\AdminBundle\Action\Account\ResetPasswordAction`                                             | `sylius_admin.controller.account.reset_password`                                     |
| `Sylius\Bundle\AdminBundle\Action\RemoveAvatarAction`                                                      | `sylius_admin.controller.remove_avatar`                                              |
| `Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction`                                      | `sylius_admin.controller.resend_order_confirmation_email`                            |
| `Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction`                                   | `sylius_admin.controller.resend_shipment_confirmation_email`                         |
| `Sylius\Bundle\AdminBundle\Action\Account\RenderRequestPasswordResetPageAction`                            | `sylius_admin.controller.account.render_request_password_reset_page`                 |
| `Sylius\Bundle\AdminBundle\Action\Account\RequestPasswordResetAction`                                      | `sylius_admin.controller.account.request_password_reset`                             |
| `sylius.controller.admin.dashboard`                                                                        | `sylius_admin.controller.dashboard`                                                  |
| `sylius.controller.customer_statistics`                                                                    | `sylius_admin.controller.customer_statistics`                                        |
| `sylius.controller.admin.notification`                                                                     | `sylius_admin.controller.notification`                                               |
| `Sylius\Bundle\AdminBundle\Controller\RemoveCatalogPromotionAction`                                        | `sylius_admin.controller.remove_catalog_promotion`                                   |
| `Sylius\Bundle\AdminBundle\Controller\RedirectHandler`                                                     | `sylius_admin.resource_controller.redirect_handler`                                  |
| `sylius.mailer.shipment_email_manager.admin`                                                               | `sylius_admin.mailer.email_manager.shipment`                                         |
| `Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType`                                                  | `sylius_admin.form.type.request_password_reset`                                      |
| `Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType`                                                    | `sylius_admin.form.type.reset_password`                                              |
| `sylius.listener.shipment_ship`                                                                            | `sylius_admin.listener.shipment_ship`                                                |
| `sylius.listener.locale`                                                                                   | `sylius_admin.listener.locale`                                                       |
| `sylius.event_subscriber.admin_cache_control_subscriber`                                                   | `sylius_admin.event_subscriber.admin_section_cache_control`                          |
| `sylius.event_subscriber.admin_filter_subscriber`                                                          | `sylius_admin.event_subscriber.admin_filter`                                         |
| `sylius.admin.menu_builder.main`                                                                           | `sylius_admin.menu_builder.main`                                                     |
| `Sylius\Bundle\AdminBundle\Console\Command\CreateAdminUserCommand`                                         | `sylius_admin.console.command.create_admin_user`                                     |
| `Sylius\Bundle\AdminBundle\Console\Command\ChangeAdminUserPasswordCommand`                                 | `sylius_admin.console.command.change_admin_user_password`                            |
| `Sylius\Bundle\AdminBundle\MessageHandler\CreateAdminUserHandler`                                          | `sylius_admin.command_handler.create_admin_user`                                     |
| `sylius.console.command_factory.question`                                                                  | `sylius_admin.console.command_factory.question`                                      |
| `sylius.context.locale.admin_based`                                                                        | `sylius_admin.context.locale.admin_based`                                            |
| `sylius.section_resolver.admin_uri_based_section_resolver`                                                 | `sylius_admin.section_resolver.admin_uri_based`                                      |
| `sylius.twig.extension.widget.admin_notification`                                                          | `sylius_admin.twig.extension.notification_widget`                                    |
| `sylius.twig.extension.shop`                                                                               | `sylius_admin.twig.extension.shop`                                                   |
| `sylius.twig.extension.channels_currencies`                                                                | `sylius_admin.twig.extension.channels_currencies`                                    |
| `Sylius\Bundle\AdminBundle\Twig\OrderUnitTaxesExtension`                                                   | `sylius_admin.twig.extension.order_unit_taxes`                                       |
| `Sylius\Bundle\AdminBundle\Twig\ChannelNameExtension`                                                      | `sylius_admin.twig.extension.channel_name`                                           |
| **ApiBundle**                                                                                              |                                                                                      |
| `Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProvider`                                   | `sylius_api.provider.payment_configuration`                                          |
| `sylius.api.applicator.archiving_promotion`                                                                | `sylius_api.applicator.archiving_promotion`                                          |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\RegisterShopUserHandler`                                   | `sylius_api.command_handler.account.register_shop_user`                              |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler`                                            | `sylius_api.command_handler.cart.pickup_cart`                                        |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\AddItemToCartHandler`                                         | `sylius_api.command_handler.cart.add_item_to_cart`                                   |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\RemoveItemFromCartHandler`                                    | `sylius_api.command_handler.cart.remove_item_from_cart`                              |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\InformAboutCartRecalculationHandler`                          | `sylius_api.command_handler.cart.inform_about_cart_recalculation`                    |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler`                                        | `sylius_api.command_handler.checkout.update_cart`                                    |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChooseShippingMethodHandler`                              | `sylius_api.command_handler.checkout.choose_shipping_method`                         |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChoosePaymentMethodHandler`                               | `sylius_api.command_handler.checkout.choose_payment_method`                          |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\CompleteOrderHandler`                                     | `sylius_api.command_handler.checkout.complete_order`                                 |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ShipShipmentHandler`                                      | `sylius_api.command_handler.checkout.ship_shipment`                                  |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangePaymentMethodHandler`                                | `sylius_api.command_handler.account.change_payment_method`                           |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\ChangeItemQuantityInCartHandler`                              | `sylius_api.command_handler.cart.change_item_quantity_in_cart`                       |
| `Sylius\Bundle\ApiBundle\CommandHandler\Catalog\AddProductReviewHandler`                                   | `sylius_api.command_handler.catalog.add_product_review`                              |
| `Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler`                                             | `sylius_api.command_handler.cart.blame_cart`                                         |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler`                             | `sylius_api.command_handler.account.change_shop_user_password`                       |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler`                          | `sylius_api.command_handler.account.request_reset_password_token`                    |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler`                        | `sylius_api.command_handler.account.request_shop_user_verification`                  |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler`                                      | `sylius_api.command_handler.account.reset_password`                                  |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler`                       | `sylius_api.command_handler.account.send_account_registration_email`                 |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler`                      | `sylius_api.command_handler.account.send_shop_user_verification_email`               |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler`                             | `sylius_api.command_handler.checkout.send_order_confirmation`                        |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler`                             | `sylius_api.command_handler.account.send_reset_password_email`                       |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler`                     | `sylius_api.command_handler.checkout.send_shipment_confirmation_email`               |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler`                                     | `sylius_api.command_handler.account.verify_shop_user`                                |
| `Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler`                                         | `sylius_api.command_handler.send_contract_request`                                   |
| `Sylius\Bundle\ApiBundle\CommandHandler\Promotion\GeneratePromotionCouponHandler`                          | `sylius_api.command_handler.promotion.generate_promotion_coupon`                     |
| `Sylius\Bundle\ApiBundle\CommandHandler\Customer\RemoveShopUserHandler`                                    | `sylius_api.command_handler.customer.remove_shop_user`                               |
| `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ChannelContextBuilder`                                   | `sylius_api.context_builder.channel`                                                 |
| `Sylius\Bundle\ApiBundle\SerializerContextBuilder\LocaleContextBuilder`                                    | `sylius_api.context_builder.locale`                                                  |
| `Sylius\Bundle\ApiBundle\SerializerContextBuilder\HttpRequestMethodTypeContextBuilder`                     | `sylius_api.context_builder.http_request_method_type`                                |
| `Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext`                                               | `sylius_api.context.cart.token_value_based`                                          |
| `Sylius\Bundle\ApiBundle\Controller\DeleteOrderItemAction`                                                 | `sylius_api.controller.delete_order_item`                                            |
| `Sylius\Bundle\ApiBundle\Controller\GetCustomerStatisticsAction`                                           | `sylius_api.controller.get_customer_statistics`                                      |
| `Sylius\Bundle\ApiBundle\Controller\GetProductBySlugAction`                                                | `sylius_api.controller.get_product_by_slug`                                          |
| `Sylius\Bundle\ApiBundle\Controller\RemoveCatalogPromotionAction`                                          | `sylius_api.controller.remove_catalog_promotion`                                     |
| `Sylius\Bundle\ApiBundle\Controller\RemoveCustomerShopUserAction`                                          | `sylius_api.controller.remove_customer_shop_user`                                    |
| `Sylius\Bundle\ApiBundle\Controller\GetStatisticsAction`                                                   | `sylius_api.controller.get_statistics`                                               |
| `Sylius\Bundle\ApiBundle\Creator\ProductImageCreator`                                                      | `sylius_api.creator.product_image`                                                   |
| `Sylius\Bundle\ApiBundle\Creator\TaxonImageCreator`                                                        | `sylius_api.creator.taxon_image`                                                     |
| `Sylius\Bundle\ApiBundle\EventHandler\OrderCompletedHandler`                                               | `sylius_api.event_handler.order_completed`                                           |
| `Sylius\Bundle\ApiBundle\EventSubscriber\ProductVariantEventSubscriber`                                    | `sylius_api.event_subscriber.product_variant`                                        |
| `Sylius\Bundle\ApiBundle\EventSubscriber\CatalogPromotionEventSubscriber`                                  | `sylius_api.event_subscriber.catalog_promotion`                                      |
| `Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber`                                     | `sylius_api.event_subscriber.kernel_request`                                         |
| `Sylius\Bundle\ApiBundle\EventSubscriber\ProductDeletionEventSubscriber`                                   | `sylius_api.event_subscriber.product_deletion`                                       |
| `Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber`                                       | `sylius_api.event_subscriber.product_slug`                                           |
| `Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber`                                     | `sylius_api.event_subscriber.taxon_deletion`                                         |
| `Sylius\Bundle\ApiBundle\EventSubscriber\TaxonSlugEventSubscriber`                                         | `sylius_api.event_subscriber.taxon_slug`                                             |
| `Sylius\Bundle\ApiBundle\EventSubscriber\AttributeEventSubscriber`                                         | `sylius_api.event_subscriber.attribute`                                              |
| `Sylius\Bundle\ApiBundle\Controller\Payment\GetPaymentConfiguration`                                       | `sylius_api.controller.payment.get_payment_configuration`                            |
| `sylius.api.provider.liip_image_filters`                                                                   | `sylius_api.provider.liip_image_filters`                                             |
| `Sylius\Bundle\ApiBundle\QueryHandler\GetCustomerStatisticsHandler`                                        | `sylius_api.query_handler.get_customer_statistics`                                   |
| `Sylius\Bundle\ApiBundle\QueryHandler\GetStatisticsHandler`                                                | `sylius_api.query_handler.get_statistics`                                            |
| `sylius_api.security.voter.order`                                                                          | `sylius_api.security.voter.order_adjustments`                                        |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\AddressDenormalizer`                                      | `sylius_api.denormalizer.address`                                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer`                             | `sylius_api.denormalizer.command_arguments`                                          |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer`                                      | `sylius_api.denormalizer.command`                                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\HydraErrorNormalizer`                                       | `sylius_api.normalizer.hydra_error`                                                  |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductNormalizer`                                          | `sylius_api.normalizer.product`                                                      |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductAttributeValueDenormalizer`                        | `sylius_api.denormalizer.product_attribute_value`                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductDenormalizer`                                      | `sylius_api.denormalizer.product`                                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductAttributeValueNormalizer`                            | `sylius_api.normalizer.product_attribute_value`                                      |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer`                                            | `sylius_api.normalizer.image`                                                        |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\CommandNormalizer`                                          | `sylius_api.normalizer.command`                                                      |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer`                                   | `sylius_api.normalizer.product_variant`                                              |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ShippingMethodNormalizer`                                   | `sylius_api.normalizer.shipping_method`                                              |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ZoneDenormalizer`                                         | `sylius_api.denormalizer.zone`                                                       |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableDenormalizer`                                 | `sylius_api.denormalizer.translatable`                                               |
| `date_time_normalizer`                                                                                     | `sylius_api.normalizer.date_time`                                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelPriceHistoryConfigDenormalizer`                    | `sylius_api.denormalizer.channel_price_history_config`                               |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelDenormalizer`                                      | `sylius_api.denormalizer.channel`                                                    |
| `sylius.api.denormalizer.numeric_to_string.tax_rate`                                                       | `sylius_api.denormalizer.numeric_to_string.tax_rate`                                 |
| `sylius.api.denormalizer.numeric_to_string.exchange_rate`                                                  | `sylius_api.denormalizer.numeric_to_string.exchange_rate`                            |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CustomerDenormalizer`                                     | `sylius_api.denormalizer.customer`                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableLocaleKeyDenormalizer`                        | `sylius_api.denormalizer.translatable_locale_key`                                    |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductVariantChannelPricingsChannelCodeKeyDenormalizer`  | `sylius_api.denormalizer.product_variant_channel_pricings_channel_code_key`          |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\DoctrineCollectionValuesNormalizer`                         | `sylius_api.normalizer.doctrine_collection_values`                                   |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmailValidator`                               | `sylius_api.validator.unique_shop_user_email`                                        |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator`                                     | `sylius_api.validator.order_not_empty`                                               |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator`                           | `sylius_api.validator.order_product_eligibility`                                     |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator`                             | `sylius_api.validator.order_item_availability`                                       |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator`                    | `sylius_api.validator.order_shipping_method_eligibility`                             |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\CheckoutCompletionValidator`                                | `sylius_api.validator.checkout_completion`                                           |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator`                   | `sylius_api.validator.chosen_shipping_method_eligibility`                            |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator`                | `sylius_api.validator.adding_eligible_product_variant_to_cart`                       |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator`                         | `sylius_api.validator.changed_item_quantity_in_cart`                                 |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator`                               | `sylius_api.validator.correct_order_address`                                         |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator`                     | `sylius_api.validator.order_payment_method_eligibility`                              |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator`                    | `sylius_api.validator.chosen_payment_method_eligibility`                             |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChangedValidator`                         | `sylius_api.validator.can_payment_method_be_changed`                                 |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator`              | `sylius_api.validator.correct_change_shop_user_confirm_password`                     |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator`                              | `sylius_api.validator.confirm_reset_password`                                        |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator`                        | `sylius_api.validator.promotion_coupon_eligibility`                                  |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator`                            | `sylius_api.validator.shipment_already_shipped`                                      |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExistsValidator`                  | `sylius_api.validator.shop_user_reset_password_token_exists`                         |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpiredValidator`              | `sylius_api.validator.shop_user_reset_password_token_not_expired`                    |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator`                               | `sylius_api.validator.shop_user_not_verified`                                        |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOptionValidator`                | `sylius_api.validator.single_value_for_product_variant_option`                       |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator`                               | `sylius_api.validator.unique_reviewer_email`                                         |
| `Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpiredValidator`                 | `sylius_api.validator.admin_reset_password_token_non_expired`                        |
| `sylius.validator.order_address_requirement`                                                               | `sylius_api.validator.order_address_requirement`                                     |
| `Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor`                                | `sylius_api.extractor.property_info.empty_property_list`                             |
| `Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver`                                  | `sylius_api.section_resolver.admin_api_uri_based`                                    |
| `Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver`                                   | `sylius_api.section_resolver.shop_api_uri_based`                                     |
| `Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener`                                              | `sylius_api.listener.api_cart_blamer`                                                |
| `sylius.listener.api_authentication_success_listener`                                                      | `sylius_api.listener.authentication_success`                                         |
| `sylius.listener.admin_api_authentication_success_listener`                                                | `sylius_api.listener.admin_authentication_success`                                   |
| **AddressingBundle**                                                                                       |                                                                                      |
| `sylius.province_naming_provider`                                                                          | `sylius.provider.province_naming`                                                    |
| `sylius.zone_matcher`                                                                                      | `sylius.matcher.zone`                                                                |
| `sylius.address_comparator`                                                                                | `sylius.comparator.address`                                                          |
| **ChannelBundle**                                                                                          |                                                                                      |
| `sylius.channel_collector`                                                                                 | `sylius.collector.channel`                                                           |
| **CurrencyBundle**                                                                                         |                                                                                      |
| `sylius.currency_converter`                                                                                | `sylius.converter.currency`                                                          |
| `sylius.currency_name_converter`                                                                           | `sylius.converter.currency_name`                                                     |
| **InventoryBundle**                                                                                        |                                                                                      |
| `sylius.availability_checker.default`                                                                      | `sylius.availability_checker`                                                        |
| **LocaleBundle**                                                                                           |                                                                                      |
| `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext`                                       | `sylius.context.locale.request_header_based`                                         |
| `sylius.locale_collection_provider`                                                                        | `sylius.provider.locale_collection`                                                  |
| `sylius.locale_collection_provider.cahced`                                                                 | `sylius.provider.locale_collection.cached`                                           |
| `sylius.locale_provider`                                                                                   | `sylius.provider.locale`                                                             |
| `sylius.locale_converter`                                                                                  | `sylius.converter.locale`                                                            |
| `Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener`                             | `sylius.doctrine.listener.locale_modification`                                       |
| **MoneyBundle**                                                                                            |                                                                                      |
| `sylius.twig.extension.convert_amount`                                                                     | `sylius.twig.extension.convert_money`                                                |
| `sylius.twig.extension.money`                                                                              | `sylius.twig.extension.format_money`                                                 |
| `sylius.money_formatter`                                                                                   | `sylius.formatter.money`                                                             |
| **OrderBundle**                                                                                            |                                                                                      |
| `sylius.order_modifier`                                                                                    | `sylius.modifier.order`                                                              |
| `sylius.order_item_quantity_modifier`                                                                      | `sylius.modifier.order_item_quantity`                                                |
| `sylius.order_number_assigner`                                                                             | `sylius.number_assigner.order_number`                                                |
| `sylius.adjustments_aggregator`                                                                            | `sylius.aggregator.adjustments_by_label`                                             |
| `sylius.expired_carts_remover`                                                                             | `sylius.remover.expired_carts`                                                       |
| `sylius.sequential_order_number_generator`                                                                 | `sylius.number_generator.sequential_order`                                           |
| `Sylius\Bundle\OrderBundle\Console\Command\RemoveExpiredCartsCommand`                                      | `sylius.console.command.remove_expired_carts`                                        |
| **PaymentBundle**                                                                                          |                                                                                      |
| `sylius.payment_methods_resolver`                                                                          | `sylius.resolver.payment_methods`                                                    |
| `sylius.payment_methods_resolver.default`                                                                  | `sylius.resolver.payment_methods.default`                                            |
| **PayumBundle**                                                                                            |                                                                                      |
| `sylius.payum_action.authorize_payment`                                                                    | `sylius_payum.action.authorize_payment`                                              |
| `sylius.payum_action.capture_payment`                                                                      | `sylius_payum.action.capture_payment`                                                |
| `sylius.payum_action.execute_same_request_with_payment_details`                                            | `sylius_payum.action.execute_same_request_with_payment_details`                      |
| `sylius.payum_action.resolve_next_route`                                                                   | `sylius_payum.action.resolve_next_route`                                             |
| `sylius.payum_action.offline.convert_payment`                                                              | `sylius_payum.action.offline.convert_payment`                                        |
| `sylius.payum_action.offline.status`                                                                       | `sylius_payum.action.offline.status`                                                 |
| `sylius.payum_action.offline.resolve_next_route`                                                           | `sylius_payum.action.offline.resolve_next_route`                                     |
| `sylius.payum_extension.update_payment_state`                                                              | `sylius_payum.extension.update_payment_state`                                        |
| `sylius.factory.payum_get_status_action`                                                                   | `sylius_payum.factory.get_status`                                                    |
| `sylius.factory.payum_resolve_next_route`                                                                  | `sylius_payum.factory.resolve_next_route`                                            |
| `sylius.form.extension.type.gateway_config.crypted`                                                        | `sylius_payum.form.extension.type.crypted_gateway_config`                            |
| `sylius.payment_description_provider`                                                                      | `sylius_payum.provider.payment_description`                                          |
| `sylius.payum.http_client`                                                                                 | `sylius_payum.http_client`                                                           |
| **ProductBundle**                                                                                          |                                                                                      |
| `sylius.form.type.sylius_product_associations`                                                             | `sylius.form.type.product_associations`                                              |
| `sylius.form.event_subscriber.product_variant_generator`                                                   | `sylius.form.event_subscriber.generate_product_variants`                             |
| `Sylius\Bundle\ProductBundle\Validator\ProductVariantOptionValuesConfigurationValidator`                   | `sylius.validator.product_variant_option_values_configuration`                       |
| `sylius.validator.product_code_uniqueness`                                                                 | `sylius.validator.unique_simple_product_code`                                        |
| `sylius.product_variant_resolver.default`                                                                  | `sylius.resolver.product_variant.default`                                            |
| `sylius.available_product_option_values_resolver`                                                          | `sylius.resolver.available_product_option_values`                                    |
| **PromotionBundle**                                                                                        |                                                                                      |
| `Sylius\Bundle\PromotionBundle\Console\Command\GenerateCouponsCommand`                                     | `sylius.console.command.generate_coupons`                                            |
| `sylius.promotion_coupon_duration_eligibility_checker`                                                     | `sylius.eligibility_checker.promotion_coupon.duration`                               |
| `sylius.promotion_coupon_usage_limit_eligibility_checker`                                                  | `sylius.eligibility_checker.promotion_coupon.usage_limit`                            |
| `sylius.promotion_coupon_eligibility_checker`                                                              | `sylius.eligibility_checker.promotion_coupon`                                        |
| `sylius.promotion_duration_eligibility_checker`                                                            | `sylius.eligibility_checker.promotion.duration`                                      |
| `sylius.promotion_usage_limit_eligibility_checker`                                                         | `sylius.eligibility_checker.promotion.usage_limit`                                   |
| `sylius.promotion_subject_coupon_eligibility_checker`                                                      | `sylius.eligibility_checker.promotion.subject_coupon`                                |
| `sylius.promotion_rules_eligibility_checker`                                                               | `sylius.eligibility_checker.promotion.rules`                                         |
| `sylius.promotion_archival_eligibility_checker`                                                            | `sylius.eligibility_checker.promotion.archival`                                      |
| `sylius.promotion_eligibility_checker`                                                                     | `sylius.eligibility_checker.promotion`                                               |
| `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType`                                             | `sylius.form.type.catalog_promotion`                                                 |
| `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType`                                        | `sylius.form.type.catalog_promotion_scope`                                           |
| `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType` | `sylius.form.type.catalog_promotion_action.percentage_discount_action_configuration` |
| `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType`                                       | `sylius.form.type.catalog_promotion_action`                                          |
| `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionTranslationType`                                  | `sylius.form.type.catalog_promotion_translation`                                     |
| `Sylius\Bundle\PromotionBundle\Form\Type\PromotionTranslationType`                                         | `sylius.form.type.promotion_translation`                                             |
| `sylius.form.type.promotion_action.collection`                                                             | `sylius.form.type.promotion_action_collection`                                       |
| `sylius.form.type.promotion_rule.collection`                                                               | `sylius.form.type.promotion_rule_collection`                                         |
| `sylius.validator.date_range`                                                                              | `sylius.validator.promotion_date_range`                                              |
| `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionGroupValidator`                             | `sylius.validator.catalog_promotion_action_group`                                    |
| `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionTypeValidator`                              | `sylius.validator.catalog_promotion_action_type`                                     |
| `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeGroupValidator`                              | `sylius.validator.catalog_promotion_scope_group`                                     |
| `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeTypeValidator`                               | `sylius.validator.catalog_promotion_scope_type`                                      |
| `Sylius\Bundle\PromotionBundle\Validator\PromotionActionGroupValidator`                                    | `sylius.validator.promotion_action_group`                                            |
| `Sylius\Bundle\PromotionBundle\Validator\PromotionActionTypeValidator`                                     | `sylius.validator.promotion_action_type`                                             |
| `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator`                                      | `sylius.validator.promotion_role_group`                                              |
| `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleTypeValidator`                                       | `sylius.validator.promotion_role_type`                                               |
| `Sylius\Bundle\PromotionBundle\Validator\PromotionNotCouponBasedValidator`                                 | `sylius.validator.promotion_not_coupon_based`                                        |
| `sylius.promotion_processor`                                                                               | `sylius.processor.promotion`                                                         |
| `sylius.promotion_applicator`                                                                              | `sylius.action.applicator.promotion`                                                 |
| `sylius.registry_promotion_rule_checker`                                                                   | `sylius.registry.promotion.rule_checker`                                             |
| `sylius.registry_promotion_action`                                                                         | `sylius.registry.promotion_action`                                                   |
| `sylius.active_promotions_provider`                                                                        | `sylius.provider.active_promotions`                                                  |
| `sylius.promotion_coupon_generator`                                                                        | `sylius.generator.promotion_coupon`                                                  |
| `sylius.promotion_coupon_generator.percentage_policy`                                                      | `sylius.generator.percentage_generation_policy`                                      |
| **ReviewBundle**                                                                                           |                                                                                      |
| `sylius.average_rating_calculator`                                                                         | `sylius.calculator.average_rating`                                                   |
| `sylius.%s_review.average_rating_updater`                                                                  | `sylius.updater.%s_review.average_rating`                                            |
| **Note: `%s` refers to the entity names associated with reviews (e.g., `product`, etc.).**                 |                                                                                      |
| **ShippingBundle**                                                                                         |                                                                                      |
| `sylius.category_requirement_shipping_method_eligibility_checker`                                          | `sylius.eligibility_checker.shipping_method.category_requirement`                    |
| `sylius.shipping_method_rules_shipping_method_eligibility_checker`                                         | `sylius.eligibility_checker.shipping_method.rules`                                   |
| `sylius.shipping_method_eligibility_checker`                                                               | `sylius.eligibility_checker.shipping_method`                                         |
| `sylius.form.type.shipping_method_rule.collection`                                                         | `sylius.form.type.shipping_method_rule_collection`                                   |
| `Sylius\Bundle\ShippingBundle\Validator\ShippingMethodCalculatorExistsValidator`                           | `sylius.validator.shipping_method_calculator_exists`                                 |
| `Sylius\Bundle\ShippingBundle\Validator\ShippingMethodRuleValidator`                                       | `sylius.validator.shipping_method_rule`                                              |
| `Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator`        | `sylius.validator.groups_generator.shipping_method_configuration`                    |
| `sylius.shipping_methods_resolver`                                                                         | `sylius.resolver.shipping_methods`                                                   |
| `sylius.shipping_methods_resolver.default`                                                                 | `sylius.resolver.shipping_methods.default`                                           |
| `sylius.shipping_method_resolver.default`                                                                  | `sylius.resolver.shipping_method.default`                                            |
| `sylius.shipping_date_assigner`                                                                            | `sylius.assigner.shipping_date`                                                      |
| `sylius.shipping_method_rule_checker.total_weight_greater_than_or_equal`                                   | `sylius.rule_checker.shipping_method.total_weight_greater_than_or_equal`             |
| `sylius.shipping_method_rule_checker.total_weight_less_than_or_equal`                                      | `sylius.rule_checker.shipping_method.total_weight_less_than_or_equal`                |
| **ShopBundle**                                                                                             |                                                                                      |
| `sylius.shop.locale_switcher`                                                                              | `sylius_shop.locale_switcher`                                                        |
| `sylius.storage.locale`                                                                                    | `sylius_shop.locale_storage`                                                         |
| `sylius.context.locale.storage_based`                                                                      | `sylius_shop.context.locale.storage_based`                                           |
| `sylius.shop.locale_stripping_router`                                                                      | `sylius_shop.router.locale_stripping`                                                |
| `sylius.listener.non_channel_request_locale`                                                               | `sylius_shop.listener.non_channel_locale`                                            |
| `sylius.controller.shop.contact`                                                                           | `sylius_shop.controller.contact`                                                     |
| `sylius.controller.shop.currency_switch`                                                                   | `sylius_shop.controller.currency_switch`                                             |
| `sylius.controller.shop.locale_switch`                                                                     | `sylius_shop.controller.locale_switch`                                               |
| `sylius.controller.shop.register_thank_you`                                                                | `sylius_shop.controller.register_thank_you`                                          |
| `sylius.mailer.contact_email_manager.shop`                                                                 | `sylius_shop.mailer.email_manager.contact`                                           |
| `sylius.mailer.order_email_manager.shop`                                                                   | `sylius_shop.mailer.email_manager.order`                                             |
| `sylius.listener.shop_cart_blamer`                                                                         | `sylius_shop.listener.shop_cart_blamer`                                              |
| `sylius.listener.email_updater`                                                                            | `sylius_shop.listener.customer_email_updater`                                        |
| `sylius.listener.shop_customer_account_sub_section_cache_control_subscriber`                               | `sylius_shop.event_subscriber.shop_customer_account_sub_section_cache_control`       |
| `sylius.listener.order_customer_ip`                                                                        | `sylius_shop.listener.order_customer_ip`                                             |
| `sylius.listener.order_complete`                                                                           | `sylius_shop.listener.order_complete`                                                |
| `sylius.listener.user_registration`                                                                        | `sylius_shop.listener.user_registration`                                             |
| `sylius.listener.order_integrity_checker`                                                                  | `sylius_shop.listener.order_integrity_checker`                                       |
| `sylius.order_locale_assigner`                                                                             | `sylius_shop.listener.order_locale_assigner`                                         |
| `sylius.listener.session_cart`                                                                             | `sylius_shop.event_subscriber.session_cart`                                          |
| `sylius.listener.user_cart_recalculation`                                                                  | `sylius_shop.listener.user_cart_recalculation`                                       |
| `sylius.listener.user_impersonated`                                                                        | `sylius_shop.listener.user_impersonated`                                             |
| `sylius.shop.menu_builder.account`                                                                         | `sylius_shop.menu_builder.account`                                                   |
| `sylius.twig.extension.original_price_to_display`                                                          | `sylius_shop.twig.extension.order_item_original_price_to_display`                    |
| `Sylius\Bundle\ShopBundle\Twig\OrderPaymentsExtension`                                                     | `sylius_shop.twig.extension.order_payments`                                          |
| `sylius.section_resolver.shop_uri_based_section_resolver`                                                  | `sylius_shop.section_resolver.shop_uri_based`                                        |
| `sylius.context.cart.session_and_channel_based`                                                            | `sylius_shop.context.cart.session_and_channel_based`                                 |
| `sylius.storage.cart_session`                                                                              | `sylius_shop.storage.cart_session`                                                   |
| `sylius.grid_filter.shop_string`                                                                           | `sylius_shop.grid.filter.string`                                                     |
| **TaxationBundle**                                                                                         |                                                                                      |
| `sylius.tax_rate_resolver`                                                                                 | `sylius.resolver.tax_rate`                                                           |
| `sylius.tax_rate_date_eligibility_checker`                                                                 | `sylius.eligibility_checker.tax_rate_date`                                           |
| **TaxonomyBundle**                                                                                         |                                                                                      |
| `sylius.doctrine.odm.mongodb.unitOfWork`                                                                   | `sylius.doctrine.odm.mongodb.unit_of_work`                                           |
| **UiBundle**                                                                                               |                                                                                      |
| `Sylius\Bundle\UiBundle\Twig\RedirectPathExtension`                                                        | `sylius.twig.extension.redirect_path`                                                |
| **UserBundle**                                                                                             |                                                                                      |
| `Sylius\Bundle\UserBundle\Console\Command\DemoteUserCommand`                                               | `sylius.console.command.demote_user`                                                 |
| `Sylius\Bundle\UserBundle\Console\Command\PromoteUserCommand`                                              | `sylius.console.command.promote_user`                                                |

  The old service IDs are no longer available in Sylius 2.0. Please ensure your configurations and service references use the new service IDs.

* The following services had new aliases added in Sylius 1.14. In Sylius 2.0, these aliases have become the primary service IDs, and the old service IDs remain as aliases:

| Current ID                                                                                  | New Alias                                                       | 
|---------------------------------------------------------------------------------------------|-----------------------------------------------------------------|
| **AddressingBundle**                                                                        |                                                                 |
| `Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface`                          | `sylius.checker.zone_deletion`                                  |
| `Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface`              | `sylius.checker.country_provinces_deletion`                     |
| **ApiBundle**                                                                               |                                                                 |
| `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface`             | `sylius_api.applicator.archiving_shipping_method`               |
| `Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicatorInterface`         | `sylius_api.applicator.order_state_machine_transition`          |
| `Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface`       | `sylius_api.applicator.payment_state_machine_transition`        |
| `Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicatorInterface` | `sylius_api.applicator.product_review_state_machine_transition` |
| `Sylius\Bundle\ApiBundle\Context\UserContextInterface`                                      | `sylius_api.context.user.token_based`                           |
| `Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface`                              | `sylius_api.provider.path_prefix`                               |
| `Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface`                         | `sylius_api.provider.adjustment_order`                          |
| `Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface`                             | `sylius_api.changer.payment_method`                             |
| `Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface`                       | `sylius_api.converter.iri_to_identifier`                        |
| `Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface`                                     | `sylius_api.mapper.address`                                     |
| `Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface`                  | `sylius_api.checker.applied_coupon_eligibility`                 |
| `Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface`                            | `sylius_api.modifier.order_address`                             |
| `Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface`                      | `sylius_api.assigner.order_promotion_code`                      |
| **LocaleBundle**                                                                            |                                                                 |
| `Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface`                            | `sylius.checker.locale_usage`                                   |
| **ProductBundle**                                                                           |                                                                 |
| `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`                         | `sylius.resolver.product_variant`                               |
| **PromotionBundle**                                                                         |                                                                 |
| `Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface`         | `sylius.provider.eligible_catalog_promotions`                   |
| **TaxonomyBundle**                                                                          |                                                                 |
| `Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface`                      | `sylius.custom_repository.tree.taxon`                           |

* Aliases for the `knp_menu.menu_builder` tags introduced in Sylius 1.14 are now the only valid menu builder tags in Sylius 2.0:

| Old Alias             | New Alias             |
|-----------------------|-----------------------|
| **AdminBundle**       |                       |
| `sylius.admin.main`   | `sylius_admin.main`   |
| **ShopBundle**        |                       |
| `sylius.shop.account` | `sylius_shop.account` |

* The following classes have been renamed:

| Old Name                                                                               | New Name                                                                                |
|----------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResendVerificationEmailHandler`        | `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler`     |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler`   | `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler`   |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler`          | `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler`                  |

* The `Normalizer` and `Denormalizer` in `ApiBundle` have been reorganized into subdirectories, moving from `Sylius\Bundle\ApiBundle\Serializer`
  to `Sylius\Bundle\ApiBundle\Serializer\Normalizer` and `Sylius\Bundle\ApiBundle\Serializer\Denormalizer`.

* The `SerializerContextBuilder` classes in `ApiBundle` have been moved to the `Sylius\Bundle\ApiBundle\Serializer\ContextBuilder` subdirectory.

* The `Filter`, `QueryCollectionExtension`, and `QueryItemExtension` classes in `ApiBundle` have been reorganized into separate sections (`admin`, `shop`, `common`)
  based on their usage context, and grouped by resources.

* The definition of the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType` was moved to the `CoreBundle`.

* The definition of the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType` was moved to the `PromotionBundle`.

* The following parameters were removed:

    * `sylius.mongodb_odm.repository.class`
    * `sylius.phpcr_odm.repository.class`
    
* The following parameters were renamed:

    * `sylius.message.admin_user_create.validation_groups` to `sylius_admin.command_handler.create_admin_user.validation_groups`

* The following configuration options were removed:

    * `sylius.mailer.templates`

* Added the `Sylius\Component\Order\Context\ResettableCartContextInterface` that
  extends `Sylius\Component\Order\Context\CartContextInterface` and `Symfony\Contracts\Service\ResetInterface`.

* The name of the default `LiipImagineBundle`'s resolver and loader were changed from **default** to **sylius_image
  ** ([reference](https://github.com/Sylius/Sylius/pull/12543)).
  To change the default resolver and/or loader for `LiipImagineBundle`, configure `cache` and/or `data_loader`
  parameters under the `liip_imagine` key.

* The class `Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteSubscriber` has been removed and replaced with
  `Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteListener`.

* The `sylius/calendar` package has been replaced with `symfony/clock` package. All usages of
  the `Sylius\Calendar\Provider\DateTimeProviderInterface` class
  have been replaced with `Symfony\Component\Clock\ClockInterface` class.

    * The following classes were changed:

        * `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer`
        * `Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account\RequestResetPasswordEmailHandler`
        * `Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger`
        * `Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover`
        * `Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssigner`
        * `Sylius\Bundle\PromotionBundle\Criteria\DateRange`
        * `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicator`
        * `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler`
        * `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler`
        * `Sylius\Component\Taxation\Checker\TaxRateDateEligibilityChecker`

* The parameter order of `Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType::__construct` has been changed:

    ```php
        public function __construct(
        +   private readonly AddressComparatorInterface $addressComparator,
            string $dataClass,
            array $validationGroups = []
        -   private readonly AddressComparatorInterface $addressComparator = null,
        )
    ```

* The `\Serializable` interface has been removed from the `Sylius\Component\User\Model\UserInterface`.

* The parameter order of the `Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor::__construct` has been
  changed:

    ```php
        public function __construct(
            private OrderPaymentProviderInterface $orderPaymentProvider,
        -   private string $targetState = PaymentInterface::STATE_CART,
            private OrderPaymentsRemoverInterface $orderPaymentsRemover,
            private array $unprocessableOrderStates,
        +   private string $targetState = PaymentInterface::STATE_CART,
        )
    ```

* The following repository classes and interfaces were added, if you have custom repositories,
  you need to update them to extend the new ones:

  Addressing:

    * `Sylius\Bundle\AddressingBundle\Doctrine\ORM\AddressRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Addressing\Repository\AddressRepositoryInterface`
    * `Sylius\Bundle\AddressingBundle\Doctrine\ORM\CountryRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Addressing\Repository\CountryRepositoryInterface`
    * `Sylius\Bundle\AddressingBundle\Doctrine\ORM\ProvinceRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Addressing\Repository\ProvinceRepositoryInterface`
    * `Sylius\Bundle\AddressingBundle\Doctrine\ORM\ZoneMemberRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Addressing\Repository\ZoneMemberRepositoryInterface`

  Attribute:

    * `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Attribute\Repository\AttributeRepositoryInterface`
    * `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeTranslationRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Attribute\Repository\AttributeTranslationRepositoryInterface`
    * `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeValueRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Attribute\Repository\AttributeValueRepositoryInterface`

  Product:

    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationTypeTranslationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeTranslationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionTranslationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionValueRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionValueTranslationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductTranslationRepository`
    * `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantTranslationRepository`

  Currency:

    * `Sylius\Bundle\CurrencyBundle\Doctrine\ORM\CurrencyRepository`
      extends `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository`
      implements `Sylius\Component\Currency\Repository\CurrencyRepositoryInterface`

* The following repository classes and interfaces namespaces were changed, if you have custom repositories,
  you need to update them to extend the new ones:

  Addressing:

    * `Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository` extended class changed from
      `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository` to
      `Sylius\Bundle\AddressingBundle\Doctrine\ORM\AddressRepository`
    * `Sylius\Component\Core\Repository\AddressRepositoryInterface` implemented interface changed from
      `Sylius\Component\Resource\Repository\RepositoryInterface` to
      `Sylius\Component\Addressing\Repository\AddressRepositoryInterface`

  Attribute:

    * `Sylius\Bundle\CoreBundle\Doctrine\ORM\AttributeRepository` extended class changed from
      `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository` to
      `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeRepository`
    * `Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface` extended interface changed from
      `Sylius\Component\Resource\Repository\RepositoryInterface` to
      `Sylius\Component\Attribute\Repository\AttributeValueRepositoryInterface`

  Product:

    * `Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductAssociationRepository` extended class changed from
      `Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository` to
      `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationRepository`

* A new parameter has been added to specify the validation groups for a given zone.
  If you have any custom validation groups for zone member, you need to add them to
  your `config/packages/_sylius.yaml` file.
  This is handled by `Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroup` and it resolves the groups
  based on the type of the passed zone.

* The following constructor parameter has been changed across the codebase:

    ```php
    -   private StateMachineInterface $stateMachineFactory,
    +   private StateMachineInterface $stateMachine,
    ```    

  ```yaml
  sylius_addressing:
    zone_member:
      validation_groups:
        country:
          - 'sylius'
          - 'sylius_zone_member_country'
        zone:
          - 'sylius'
          - 'sylius_zone_member_zone'
  ```

* The `locked`, `expiresAt` and `credentialsExpireAt` fields have been removed from the User model, both ShopUser and AdminUser, 
  as well as the corresponding methods in the User model and columns in the database tables.

* The following classes and interfaces have been removed:

    * `Sylius\Bundle\CoreBundle\Templating\Helper\CheckoutStepsHelper`
    * `Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper`
    * `Sylius\Bundle\CoreBundle\Templating\Helper\VariantResolverHelper`
    * `Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper`
    * `Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface`
    * `Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper`
    * `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper`
    * `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface`
    * `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper`
    * `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface`
    * `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper`
    * `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface`
    * `Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper`
    * `Sylius\Bundle\UserBundle\Security\UserLogin`
    * `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
    * `Sylius\Bundle\UserBundle\Security\UserPasswordHasher`
    * `Sylius\Bundle\UserBundle\Security\UserPasswordHasherInterface`
    * `Sylius\Component\User\Security\Generator\UniquePinGenerator`

* The following services and aliases have been removed:

    * `sylius.security.password_hasher`
    * `sylius.security.user_login`
    * `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
    * `Sylius\Component\User\Security\UserPasswordHasherInterface`

* The service `sylius.form_registry.payum_gateway_config` has been moved to the `PaymentBundle`, and its ID changed to `sylius.form_registry.payment_gateway_config`.

* The class `Sylius\Bundle\PayumBundle\Validator\GatewayFactoryExistsValidator` has been moved to the `PaymentBundle`, and its service ID changed to`sylius.validator.gateway_factory_exists`.

* The class `Sylius\Bundle\PayumBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator` has been moved to the `PaymentBundle`, and its service ID changed to`sylius.validator.groups_generator.gateway_config`.

### Constructors signature changes

1. The following constructor signatures have been changed:

   `Sylius\Bundle\CoreBundle\Twig\CheckoutStepsExtension`
    ```diff
    
    use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
    use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;

        public function __construct(
    -       private readonly CheckoutStepsHelper|OrderPaymentMethodSelectionRequirementCheckerInterface $checkoutStepsHelper,
    -       private readonly ?OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker = null,
    +       private readonly OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
    +       private readonly OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        )
    ```

   `Sylius\Bundle\CoreBundle\Twig\PriceExtension`
    ```diff
    
    use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;

        public function __construct(
    -       private readonly PriceHelper|ProductVariantPricesCalculatorInterface $helper,
    +       private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator,
        )
    ```

   `Sylius\Bundle\CoreBundle\Twig\VariantResolverExtension`
    ```diff
    
    use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

        public function __construct(
    -       private readonly ProductVariantResolverInterface|VariantResolverHelper $helper,
    +       private readonly ProductVariantResolverInterface $productVariantResolver,
        )
    ```

   `Sylius\Bundle\CurrencyBundle\Twig\CurrencyExtension`
    ```diff

        public function __construct(
    -       private ?CurrencyHelperInterface $helper = null,
        )
    ```

   `Sylius\Bundle\InventoryBundle\Twig\InventoryExtension`
    ```diff
    use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

        public function __construct(
    -       private AvailabilityCheckerInterface|InventoryHelper $helper,
    +       private AvailabilityCheckerInterface $availabilityChecker,
        )
    ```

   `Sylius\Bundle\LocaleBundle\Twig\LocaleExtension`
    ```diff
    use Sylius\Component\Locale\Context\LocaleContextInterface;
    use Sylius\Component\Locale\Converter\LocaleConverterInterface;

        public function __construct(
    -       private LocaleConverterInterface|LocaleHelperInterface $localeHelper,
    -       private ?LocaleContextInterface $localeContext = null,
    +       private LocaleConverterInterface $localeConverter,
    +       private LocaleContextInterface $localeContext,
        )
    ```

   `Sylius\Bundle\MoneyBundle\Twig\ConvertMoneyExtension`
    ```diff
    use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

        public function __construct(
    -       private ConvertMoneyHelperInterface|CurrencyConverterInterface $helper,
    +       private CurrencyConverterInterface $currencyConverter,
        )
    ```

   `Sylius\Bundle\MoneyBundle\Twig\FormatMoneyExtension`
    ```diff
    use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

        public function __construct(
    -       private private FormatMoneyHelperInterface|MoneyFormatterInterface $helper,
    +       private MoneyFormatterInterface $moneyFormatter,
        )
    ```

   `Sylius\Bundle\OrderBundle\Twig\AggregateAdjustmentsExtension`
    ```diff
    use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;

        public function __construct(
    -       private AdjustmentsAggregatorInterface|AdjustmentsHelper $adjustmentsHelper,
    +       private AdjustmentsAggregatorInterface $adjustmentsAggregator,
        )
    ```

   `Sylius\Bundle\AdminBundle\Controller\DashboardController`
    ```diff
        public function __construct(
            private ChannelRepositoryInterface $channelRepository,
            private Environment $templatingEngine,
            private RouterInterface $router,
    -       private ?StatisticsDataProviderInterface $statisticsDataProvider = null,
        )
    ```

   `Sylius\Bundle\AdminBundle\EventListener\AdminFilterSubscriber`
    ```diff
    - use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
    + use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface;

        public function __construct(private FilterStorageInterface $filterStorage)
    ```

   `Sylius\Bundle\AdminBundle\Controller\RedirectHandler`
    ```diff
    - use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
    + use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface;

        public function __construct(
            private RedirectHandlerInterface $decoratedRedirectHandler,
            private FilterStorageInterface $filterStorage,
        )
    ```

   `Sylius\Bundle\UiBundle\Twig\RedirectPathExtension`
    ```diff
    - use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
    + use Sylius\Bundle\GridBundle\Storage\FilterStorageInterface;

        public function __construct(
            private FilterStorageInterface $filterStorage,
            private RouterInterface $router,
        )
    ```

## Grids

The experimental `entities` filter has been removed. It has been replaced by the generic `entity` one.

```diff
sylius_grid:
    grids:
        # ...
        sylius_admin_catalog_promotion:
            # ...
            filters:
                channel:
-                   type: entities
+                   type: entity
                    label: sylius.ui.channel
                    form_options:
                        class: "%sylius.model.channel.class%"
                    options:
-                       field: product.channels.id
+                       fields: [product.channels.id]
```

The following service has been removed:

    - sylius.grid_filter.entities

The following classes have been removed:

    - Sylius\Component\Core\Grid\Filter\EntitiesFilter
    - Sylius\Bundle\CoreBundle\Form\Type\Grid\Filter\EntitiesFilterType

## Security

* API firewalls have been renamed and user checkers have been configured on firewalls 
  with `security.user_checker.chain.<firewall>` service:

    ```diff
    security:
        firewalls:
            admin:
                ...
    +           user_checker: security.user_checker.chain.admin
    -       new_api_admin_user:
    +       api_admin:
                ...
    +           user_checker: security.user_checker.chain.api_admin
    -       new_api_shop_user:
    +       api_shop:
                ...
    +           user_checker: security.user_checker.chain.api_shop
            shop:
                ...
    +           user_checker: security.user_checker.chain.shop
    ```

## Password Encoder & Salt
The encoder and salt has been removed from the User entities. It will use the password hasher configured on Symfony security configuration.

This "encoder" is configured via the [Symfony security password hasher](https://symfony.com/doc/current/security/passwords.html#configuring-a-password-hasher).

You may have already something like that configuration bellow.

```yaml
# config/packages/security.yaml
security:
    # ...

    password_hashers:
        Sylius\Component\User\Model\UserInterface: argon2i
```

Check if you have an encoder configured in the `sylius_user` package configuration.

```yaml
sylius_user:
    # ...
    
    encoder: plaintext # Remove this line

    # ...
    resources:
        oauth:
            user:
                encoder: false # Remove this line
                classes: Sylius\Component\User\Model\UserOAuth
```

Check your user hashed passwords in your production database.
In modern Symfony projects, the hasher name is stored on the password.

Example:
`$argon2i$v=19$m=65536,t=4,p=1$VVJuMnpUUWhRY1daN1ppMA$2Tx6l3I+OUx+PUPn+vZz1jI3Z6l6IHh2kpG0NdpmYWE`

If some of your users do not have the hasher name stored in the password field you may need to configure the "migrate_from" option into Symfony, following that documentation:
https://symfony.com/doc/current/security/passwords.html#configure-a-new-hasher-using-migrate-from

Note:
If your app never changed the hasher name configuration, you don't need to configure this "migrate_from" configuration.

## Directory structure

* The Winzou state machine configuration file `state_machine.yml` has been moved from `@SyliusPaymentBundle/Resources/config/app` to `@SyliusPaymentBundle/Resources/config/app/state_machine` and renamed to `sylius_payment.yaml`.
* The Symfony workflow configuration file `state_machine.yaml` has been moved from `@SyliusPaymentBundle/Resources/config/workflow` to `@SyliusPaymentBundle/Resources/config/app/workflow` and renamed to `sylius_payment.yaml`.

## Frontend

* `use_webpack` option was removed from the `sylius_ui` configuration, and the Webpack has become the only module
  bundler provided by Sylius.
* `use_webpack` twig global variable was removed. Webpack is always used now, and there is no need to check for it.

* Some Twig extension services have been moved from the UiBundle to the new Twig Extra package

* The following classes have been removed:
    * `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController`
    * `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
    * `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionSvopeTypeExtension`
    * `Sylius\Bundle\AdminBundle\Menu\CustomerShowMenuBuilder`
    * `Sylius\Bundle\AdminBundle\Menu\PromotionUpdateMenuBuilder`
    * `Sylius\Bundle\AdminBundle\Menu\OrderShowMenuBuilder`
    * `Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder`
    * `Sylius\Bundle\AdminBundle\Menu\ProductUpdateMenuBuilder`
    * `Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder`
    * **ApiBundle**
    * `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ReadOperationContextBuilder`
    * `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
    * `Sylius\Bundle\ApiBundle\Controller\UploadProductImageAction`
    * `Sylius\Bundle\ApiBundle\Controller\UploadTaxonImageAction`
    * `Sylius\Bundle\ApiBundle\Controller\GetOrderAdjustmentsAction`
    * `Sylius\Bundle\ApiBundle\Controller\GetAddressLogEntryCollectionAction`
    * `Sylius\Bundle\ApiBundle\DataPersister\AddressDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\AdminUserDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ChannelDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\MessengerDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\CountryDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\LocaleDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ShippingMethodDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\PaymentMethodDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ProductDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ProductAttributeDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ProductTaxonDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ProductVariantDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\PromotionCouponDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\TranslatableDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\ZoneDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\PromotionDataPersister`
    * `Sylius\Bundle\ApiBundle\DataPersister\CustomerDataPersister`
    * `Sylius\Bundle\ApiBundle\DataProvider\AccountResetPasswordItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\AdminResetPasswordItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\OrderItemItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\OrderItemUnitItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\PaymentItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ShipmentItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ProductItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ProductAttributesSubresourceDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\CustomerItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ShippingMethodsCollectionDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\PaymentMethodsCollectionDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ChannelsCollectionDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\OrderAdjustmentsSubresourceDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\OrderItemAdjustmentsSubresourceDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\VerifyCustomerAccountItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\AdminOrderItemAdjustmentsSubresourceDataProvider`
    * `Sylius\Bundle\ApiBundle\DataProvider\ChannelAwareItemDataProvider`
    * `Sylius\Bundle\ApiBundle\DataTransformer\ChannelCodeAwareInputCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface`
    * `Sylius\Bundle\ApiBundle\DataTransformer\LocaleCodeAwareInputCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailAwareCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailIfNotSetAwareCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserIdAwareCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\OrderTokenValueAwareInputCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\ShipmentIdAwareInputCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\DataTransformer\SubresourceIdAwareCommandDataTransformer`
    * `Sylius\Bundle\ApiBundle\QueryHandler\GetAddressLogEntryCollectionHandler`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\RouteNameResolver`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\CachedRouteNameResolver`
    * `Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer`
    * `Sylius\Bundle\ApiBundle\Validator\Constraints\AccountVerificationTokenEligibilityValidator`
    * `Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\ApiResourceConfigurationMerger`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\ResourceMetadataPropertyValueResolver`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\LegacyResourceMetadataMerger`
    * `Sylius\Bundle\ApiBundle\ApiPlatform\Factory\MergingExtractorResourceMetadataFactory`
    * `Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidator`
    * `Sylius\Bundle\ApiBundle\Validator\ResourceInputDataPropertiesValidatorInterface`
    * `api_platform.action.post_item`
    * **PayumBundle**
    * `Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout\ConvertPaymentAction`
    * `Sylius\Bundle\PayumBundle\Controller\PayumController`
    * `Sylius\Bundle\PayumBundle\Form\Type\PaypalGatewayConfigurationType`
    * `Sylius\Bundle\PayumBundle\Form\Type\StripeGatewayConfigurationType`
    * **UiBundle**
    * `Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand`
    * `Sylius\Bundle\UiBundle\ContextProvider\DefaultContextProvider`
    * `Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface`
    * `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockRenderingHistory`
    * `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockDataCollector`
    * `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateBlockRenderer`
    * `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateEventRenderer`
    * `Sylius\Bundle\UiBundle\Registry\TemplateBlock`
    * `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistry`
    * `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface`
    * `Sylius\Bundle\UiBundle\Renderer\DelegatingTemplateEventRenderer`
    * `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateBlockRenderer`
    * `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateEventRenderer`
    * `Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface`
    * `Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface`
    * `Sylius\Bundle\UiBundle\Renderer\TwigTemplateBlockRenderer`
    * `Sylius\Bundle\UiBundle\Storage\FilterStorage`
    * `Sylius\Bundle\UiBundle\Storage\FilterStorageInterface`
    * `Sylius\Bundle\UiBundle\Twig\LegacySonataBlockExtension`
    * `Sylius\Bundle\UiBundle\Twig\TemplateEventExtension`
    * `Sylius\Bundle\UiBundle\Twig\TestHtmlAttributeExtension`
    * `Sylius\Bundle\UiBundle\Twig\TestFormAttributeExtension`
    * `Sylius\Bundle\UiBundle\Twig\SortByExtension`

* The following services have been renamed:
    * `sylius.twig.extension.form_test_attribute_array` => `sylius_twig_extra.twig.extension.test_form_attribute`
    * `sylius.twig.extension.form_test_attribute_name` => `sylius_twig_extra.twig.extension.test_html_attribute`
    * `sylius.twig.extension.sort_by` => `sylius_twig_extra.twig.extension.sort_by`
    * `Sylius\Bundle\UiBundle\Twig\RouteExistsExtension` => `sylius_twig_extra.twig.extension.route_exists`

## Payment method gateways

* Stripe gateway has been removed. This implementation has been deprecated and not SCA Ready.
* PayPal Express Checkout gateway has been removed. Use now [PayPal Commerce Platform](https://github.com/Sylius/PayPalPlugin) integration.

## Theming
* Dependency on `sylius/theme-bundle` is moved from CoreBundle to ShopBundle and it will no longer be installed 
  if you're running your shop in headless mode.
* Channel's `themeName` form field existence is made optional and depends on `ShopBundle` presence.
* The `Sylius\Bundle\CoreBundle\Theme\ChannelBasedThemeContext` has been moved to the `Sylius\Bundle\ShopBundle\Theme\ChannelBasedThemeContext`.

## Routing

* Routing for the `sylius_shop_payum` has been moved from the `SyliusShopBundle` to the `SyliusPayumBundle`:

```yaml
sylius_shop_payum:
-   resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"
+   resource: "@SyliusPayumBundle/Resources/config/routing/integrations/sylius_shop.yaml"
```
