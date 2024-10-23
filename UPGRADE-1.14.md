# UPGRADE FROM `v1.13.X` TO `v1.14.0`

### Dependencies

1. The minimum version of `sylius/resource` and `sylius/resource-bundle`  have been bumped to `^1.11`.
   Due to that the following namespaces have been updated throughout the codebase:

| Old namespace                                                                       | New namespace                                                             |
|-------------------------------------------------------------------------------------|---------------------------------------------------------------------------|
| `Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent`                        | `Sylius\Resource\Symfony\EventDispatcher\GenericEvent`                    |
| `Sylius\Component\Resource\Exception\DeleteHandlingException`                       | `Sylius\Resource\Exception\DeleteHandlingException`                       |
| `Sylius\Component\Resource\Exception\RaceConditionException`                        | `Sylius\Resource\Exception\RaceConditionException`                        |
| `Sylius\Component\Resource\Exception\UnexpectedTypeException`                       | `Sylius\Resource\Exception\UnexpectedTypeException`                       |
| `Sylius\Component\Resource\Exception\UnsupportedMethodException`                    | `Sylius\Resource\Exception\UnsupportedMethodException`                    |
| `Sylius\Component\Resource\Exception\VariantWithNoOptionsValuesException`           | `Sylius\Resource\Exception\VariantWithNoOptionsValuesException`           |
| `Sylius\Component\Resource\Factory\FactoryInterface`                                | `Sylius\Resource\Factory\FactoryInterface`                                |
| `Sylius\Component\Resource\Generator\RandomnessGeneratorInterface`                  | `Sylius\Resource\Generator\RandomnessGeneratorInterface`                  |
| `Sylius\Component\Resource\Metadata\MetadataInterface`                              | `Sylius\Resource\Metadata\MetadataInterface`                              |
| `Sylius\Component\Resource\Metadata\Metadata`                                       | `Sylius\Resource\Metadata\Metadata`                                       |
| `Sylius\Component\Resource\Metadata\RegistryInterface`                              | `Sylius\Resource\Metadata\RegistryInterface`                              |
| `Sylius\Component\Resource\Model\CodeAwareInterface`                                | `Sylius\Resource\Model\CodeAwareInterface`                                |
| `Sylius\Component\Resource\Model\ResourceInterface`                                 | `Sylius\Resource\Model\ResourceInterface`                                 |
| `Sylius\Component\Resource\Model\TranslatableInterface`                             | `Sylius\Resource\Model\TranslatableInterface`                             |
| `Sylius\Component\Resource\Repository\InMemoryRepository`                           | `Sylius\Resource\Doctrine\Persistence\InMemoryRepository`                 |
| `Sylius\Component\Resource\Repository\RepositoryInterface`                          | `Sylius\Resource\Doctrine\Persistence\RepositoryInterface`                |
| `Sylius\Component\Resource\ResourceActions`                                         | `Sylius\Resource\ResourceActions`                                         |
| `Sylius\Component\Resource\StateMachine\StateMachine`                               | `Sylius\Resource\StateMachine\StateMachine`                               |
| `Sylius\Component\Resource\Storage\StorageInterface`                                | `Sylius\Resource\Storage\StorageInterface`                                |
| `Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface` | `Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface` |
| `Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface`   | `Sylius\Resource\Translation\TranslatableEntityLocaleAssignerInterface`   |

   The previous namespaces are still usable, but are considered deprecated and may be removed in future versions of `Resource` packages, update them at your own convenience.

### Deprecations

1. Aliases for the following services have been introduced to standardize service IDs and will replace the incorrect IDs in Sylius 2.0:
    
    | Old ID                                                                                                              | New ID                                                                                              |
    |---------------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------|
    | **AddressingBundle**                                                                                                |                                                                                                     |
    | `sylius.province_naming_provider`                                                                                   | `sylius.provider.province_naming`                                                                   |
    | `sylius.zone_matcher`                                                                                               | `sylius.matcher.zone`                                                                               |
    | `sylius.address_comparator`                                                                                         | `sylius.comparator.address`                                                                         |
    | **AdminBundle**                                                                                                     |                                                                                                     |
    | `sylius.security.shop_user_impersonator`                                                                            | `sylius_admin.security.shop_user_impersonator`                                                      |
    | `sylius.controller.impersonate_user`                                                                                | `sylius_admin.controller.impersonate_user`                                                          |
    | `Sylius\Bundle\AdminBundle\Action\Account\RenderResetPasswordPageAction`                                            | `sylius_admin.controller.account.render_reset_password_page`                                        |
    | `Sylius\Bundle\AdminBundle\Action\Account\ResetPasswordAction`                                                      | `sylius_admin.controller.account.reset_password`                                                    |
    | `Sylius\Bundle\AdminBundle\Action\RemoveAvatarAction`                                                               | `sylius_admin.controller.remove_avatar`                                                             |
    | `Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction`                                               | `sylius_admin.controller.resend_order_confirmation_email`                                           |
    | `Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction`                                            | `sylius_admin.controller.resend_shipment_confirmation_email`                                        |
    | `Sylius\Bundle\AdminBundle\Action\Account\RenderRequestPasswordResetPageAction`                                     | `sylius_admin.controller.account.render_request_password_reset_page`                                |
    | `Sylius\Bundle\AdminBundle\Action\Account\RequestPasswordResetAction`                                               | `sylius_admin.controller.account.request_password_reset`                                            |
    | `sylius.controller.admin.dashboard`                                                                                 | `sylius_admin.controller.dashboard`                                                                 |
    | `sylius.controller.customer_statistics`                                                                             | `sylius_admin.controller.customer_statistics`                                                       |
    | `sylius.controller.admin.notification`                                                                              | `sylius_admin.controller.notification`                                                              |
    | `Sylius\Bundle\AdminBundle\Controller\RemoveCatalogPromotionAction`                                                 | `sylius_admin.controller.remove_catalog_promotion`                                                  |
    | `Sylius\Bundle\AdminBundle\Controller\RedirectHandler`                                                              | `sylius_admin.resource_controller.redirect_handler`                                                 |
    | `sylius.mailer.shipment_email_manager.admin`                                                                        | `sylius_admin.mailer.shipment_email_manager`                                                        |
    | `Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType`                                                           | `sylius_admin.form.type.request_password_reset`                                                     |
    | `Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType`                                                             | `sylius_admin.form.type.reset_password`                                                             |
    | `sylius.listener.shipment_ship`                                                                                     | `sylius_admin.listener.shipment_ship`                                                               |
    | `sylius.listener.locale`                                                                                            | `sylius_admin.listener.locale`                                                                      |
    | `sylius.event_subscriber.admin_cache_control_subscriber`                                                            | `sylius_admin.event_subscriber.admin_section_cache_control`                                         |
    | `sylius.event_subscriber.admin_filter_subscriber`                                                                   | `sylius_admin.event_subscriber.admin_filter`                                                        |
    | `sylius.admin.menu_builder.main`                                                                                    | `sylius_admin.menu_builder.main`                                                                    |
    | `Sylius\Bundle\AdminBundle\Console\Command\CreateAdminUserCommand`                                                  | `sylius_admin.console.command.create_admin_user`                                                    |
    | `Sylius\Bundle\AdminBundle\Console\Command\ChangeAdminUserPasswordCommand`                                          | `sylius_admin.console.command.change_admin_user_password`                                           |
    | `Sylius\Bundle\AdminBundle\MessageHandler\CreateAdminUserHandler`                                                   | `sylius_admin.command_handler.create_admin_user`                                                    |
    | `sylius.console.command_factory.question`                                                                           | `sylius_admin.console.command_factory.question`                                                     |
    | `sylius.context.locale.admin_based`                                                                                 | `sylius_admin.context.locale.admin_based`                                                           |
    | `sylius.section_resolver.admin_uri_based_section_resolver`                                                          | `sylius_admin.section_resolver.admin_uri_based`                                                     |
    | `sylius.twig.extension.widget.admin_notification`                                                                   | `sylius_admin.twig.extension.notification_widget`                                                   |
    | `sylius.twig.extension.shop`                                                                                        | `sylius_admin.twig.extension.shop`                                                                  |
    | `sylius.twig.extension.channels_currencies`                                                                         | `sylius_admin.twig.extension.channels_currencies`                                                   |
    | `Sylius\Bundle\AdminBundle\Twig\OrderUnitTaxesExtension`                                                            | `sylius_admin.twig.extension.order_unit_taxes`                                                      |
    | `Sylius\Bundle\AdminBundle\Twig\ChannelNameExtension`                                                               | `sylius_admin.twig.extension.channel_name`                                                          |
    | **ApiBundle**                                                                                                       |                                                                                                     |
    | `Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProvider`                                            | `sylius_api.provider.payment_configuration`                                                         |
    | `sylius.api.applicator.archiving_promotion`                                                                         | `sylius_api.applicator.archiving_promotion`                                                         |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\RegisterShopUserHandler`                                            | `sylius_api.command_handler.account.register_shop_user`                                             |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\PickupCartHandler`                                                     | `sylius_api.command_handler.cart.pickup_cart`                                                       |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\AddItemToCartHandler`                                                  | `sylius_api.command_handler.cart.add_item_to_cart`                                                  |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\RemoveItemFromCartHandler`                                             | `sylius_api.command_handler.cart.remove_item_from_cart`                                             |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\InformAboutCartRecalculationHandler`                                   | `sylius_api.command_handler.cart.inform_about_cart_recalculation`                                   |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler`                                                 | `sylius_api.command_handler.checkout.update_cart`                                                   |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChooseShippingMethodHandler`                                       | `sylius_api.command_handler.checkout.choose_shipping_method`                                        |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ChoosePaymentMethodHandler`                                        | `sylius_api.command_handler.checkout.choose_payment_method`                                         |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\CompleteOrderHandler`                                              | `sylius_api.command_handler.checkout.complete_order`                                                |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ShipShipmentHandler`                                               | `sylius_api.command_handler.checkout.ship_shipment`                                                 |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangePaymentMethodHandler`                                         | `sylius_api.command_handler.account.change_payment_method`                                          |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\ChangeItemQuantityInCartHandler`                                       | `sylius_api.command_handler.cart.change_item_quantity_in_cart`                                      |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Catalog\AddProductReviewHandler`                                            | `sylius_api.command_handler.catalog.add_product_review`                                             |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Cart\BlameCartHandler`                                                      | `sylius_api.command_handler.cart.blame_cart`                                                        |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\ChangeShopUserPasswordHandler`                                      | `sylius_api.command_handler.account.change_shop_user_password`                                      |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler`                                   | `sylius_api.command_handler.account.request_reset_password_token`                                   |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResendVerificationEmailHandler`                                     | `sylius_api.command_handler.account.request_shop_user_verification`                                 |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler`                                               | `sylius_api.command_handler.account.reset_password`                                                 |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler`                                | `sylius_api.command_handler.account.send_account_registration_email`                                |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler`                                | `sylius_api.command_handler.account.send_shop_user_verification_email`                              |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler`                                      | `sylius_api.command_handler.checkout.send_order_confirmation`                                       |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler`                                      | `sylius_api.command_handler.account.send_reset_password_email`                                      |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler`                              | `sylius_api.command_handler.checkout.send_shipment_confirmation_email`                              |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler`                                       | `sylius_api.command_handler.account.verify_shop_user`                                               |
    | `Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler`                                                  | `sylius_api.command_handler.send_contract_request`                                                  |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Promotion\GeneratePromotionCouponHandler`                                   | `sylius_api.command_handler.promotion.generate_promotion_coupon`                                    |
    | `Sylius\Bundle\ApiBundle\CommandHandler\Customer\RemoveShopUserHandler`                                             | `sylius_api.command_handler.customer.remove_shop_user`                                              |
    | `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ChannelContextBuilder`                                            | `sylius_api.context_builder.channel`                                                                |
    | `Sylius\Bundle\ApiBundle\SerializerContextBuilder\LocaleContextBuilder`                                             | `sylius_api.context_builder.locale`                                                                 |
    | `Sylius\Bundle\ApiBundle\SerializerContextBuilder\HttpRequestMethodTypeContextBuilder`                              | `sylius_api.context_builder.http_request_method_type`                                               |
    | `Sylius\Bundle\ApiBundle\Context\TokenValueBasedCartContext`                                                        | `sylius_api.context.cart.token_value_based`                                                         |
    | `Sylius\Bundle\ApiBundle\Controller\DeleteOrderItemAction`                                                          | `sylius_api.controller.delete_order_item`                                                           |
    | `Sylius\Bundle\ApiBundle\Controller\GetCustomerStatisticsAction`                                                    | `sylius_api.controller.get_customer_statistics`                                                     |
    | `Sylius\Bundle\ApiBundle\Controller\GetProductBySlugAction`                                                         | `sylius_api.controller.get_product_by_slug`                                                         |
    | `Sylius\Bundle\ApiBundle\Controller\RemoveCatalogPromotionAction`                                                   | `sylius_api.controller.remove_catalog_promotion`                                                    |
    | `Sylius\Bundle\ApiBundle\Controller\RemoveCustomerShopUserAction`                                                   | `sylius_api.controller.remove_customer_shop_user`                                                   |
    | `Sylius\Bundle\ApiBundle\Controller\GetStatisticsAction`                                                            | `sylius_api.controller.get_statistics`                                                              |
    | `Sylius\Bundle\ApiBundle\Creator\ProductImageCreator`                                                               | `sylius_api.creator.product_image`                                                                  |
    | `Sylius\Bundle\ApiBundle\Creator\TaxonImageCreator`                                                                 | `sylius_api.creator.taxon_image`                                                                    |
    | `Sylius\Bundle\ApiBundle\EventHandler\OrderCompletedHandler`                                                        | `sylius_api.event_handler.order_completed`                                                          |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\ProductVariantEventSubscriber`                                             | `sylius_api.event_subscriber.product_variant`                                                       |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\CatalogPromotionEventSubscriber`                                           | `sylius_api.event_subscriber.catalog_promotion`                                                     |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber`                                              | `sylius_api.event_subscriber.kernel_request`                                                        |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\ProductDeletionEventSubscriber`                                            | `sylius_api.event_subscriber.product_deletion`                                                      |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber`                                                | `sylius_api.event_subscriber.product_slug`                                                          |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber`                                              | `sylius_api.event_subscriber.taxon_deletion`                                                        |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\TaxonSlugEventSubscriber`                                                  | `sylius_api.event_subscriber.taxon_slug`                                                            |
    | `Sylius\Bundle\ApiBundle\EventSubscriber\AttributeEventSubscriber`                                                  | `sylius_api.event_subscriber.attribute`                                                             |
    | `Sylius\Bundle\ApiBundle\Controller\Payment\GetPaymentConfiguration`                                                | `sylius_api.controller.payment.get_payment_configuration`                                           |
    | `sylius.api.provider.liip_image_filters`                                                                            | `sylius_api.provider.liip_image_filters`                                                            |
    | `Sylius\Bundle\ApiBundle\QueryHandler\GetCustomerStatisticsHandler`                                                 | `sylius_api.query_handler.get_customer_statistics`                                                  |
    | `Sylius\Bundle\ApiBundle\QueryHandler\GetStatisticsHandler`                                                         | `sylius_api.query_handler.get_statistics`                                                           |
    | `sylius_api.security.voter.order`                                                                                   | `sylius_api.security.voter.order_adjustments`                                                       |
    | `Sylius\Bundle\ApiBundle\Serializer\AddressDenormalizer`                                                            | `sylius_api.denormalizer.address`                                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\CommandArgumentsDenormalizer`                                                   | `sylius_api.denormalizer.command_arguments`                                                         |
    | `Sylius\Bundle\ApiBundle\Serializer\CommandDenormalizer`                                                            | `sylius_api.denormalizer.command`                                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\HydraErrorNormalizer`                                                           | `sylius_api.normalizer.hydra_error`                                                                 |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductNormalizer`                                                              | `sylius_api.normalizer.product`                                                                     |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductAttributeValueDenormalizer`                                              | `sylius_api.denormalizer.product_attribute_value`                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductDenormalizer`                                                            | `sylius_api.denormalizer.product`                                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductAttributeValueNormalizer`                                                | `sylius_api.normalizer.product_attribute_value`                                                     |
    | `Sylius\Bundle\ApiBundle\Serializer\ImageNormalizer`                                                                | `sylius_api.normalizer.image`                                                                       |
    | `Sylius\Bundle\ApiBundle\Serializer\CommandNormalizer`                                                              | `sylius_api.normalizer.command`                                                                     |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer`                                                       | `sylius_api.normalizer.product_variant`                                                             |
    | `Sylius\Bundle\ApiBundle\Serializer\ShippingMethodNormalizer`                                                       | `sylius_api.normalizer.shipping_method`                                                             |
    | `Sylius\Bundle\ApiBundle\Serializer\ZoneDenormalizer`                                                               | `sylius_api.denormalizer.zone`                                                                      |
    | `Sylius\Bundle\ApiBundle\Serializer\TranslatableDenormalizer`                                                       | `sylius_api.denormalizer.translatable`                                                              |
    | `date_time_normalizer`                                                                                              | `sylius_api.normalizer.date_time`                                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\ChannelPriceHistoryConfigDenormalizer`                                          | `sylius_api.denormalizer.channel_price_history_config`                                              |
    | `Sylius\Bundle\ApiBundle\Serializer\ChannelDenormalizer`                                                            | `sylius_api.denormalizer.channel`                                                                   |
    | `sylius.api.denormalizer.numeric_to_string.tax_rate`                                                                | `sylius_api.denormalizer.numeric_to_string.tax_rate`                                                |
    | `sylius.api.denormalizer.numeric_to_string.exchange_rate`                                                           | `sylius_api.denormalizer.numeric_to_string.exchange_rate`                                           |
    | `Sylius\Bundle\ApiBundle\Serializer\CustomerDenormalizer`                                                           | `sylius_api.denormalizer.customer`                                                                  |
    | `Sylius\Bundle\ApiBundle\Serializer\TranslatableLocaleKeyDenormalizer`                                              | `sylius_api.denormalizer.translatable_locale_key`                                                   |
    | `Sylius\Bundle\ApiBundle\Serializer\ProductVariantChannelPricingsChannelCodeKeyDenormalizer`                        | `sylius_api.denormalizer.product_variant_channel_pricings_channel_code_key`                         |
    | `Sylius\Bundle\ApiBundle\Serializer\DoctrineCollectionValuesNormalizer`                                             | `sylius_api.normalizer.doctrine_collection_values`                                                  |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueShopUserEmailValidator`                                        | `sylius_api.validator.unique_shop_user_email`                                                       |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderNotEmptyValidator`                                              | `sylius_api.validator.order_not_empty`                                                              |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderProductEligibilityValidator`                                    | `sylius_api.validator.order_product_eligibility`                                                    |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator`                                      | `sylius_api.validator.order_item_availability`                                                      |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator`                             | `sylius_api.validator.order_shipping_method_eligibility`                                            |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\CheckoutCompletionValidator`                                         | `sylius_api.validator.checkout_completion`                                                          |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenShippingMethodEligibilityValidator`                            | `sylius_api.validator.chosen_shipping_method_eligibility`                                           |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\AddingEligibleProductVariantToCartValidator`                         | `sylius_api.validator.adding_eligible_product_variant_to_cart`                                      |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ChangedItemQuantityInCartValidator`                                  | `sylius_api.validator.changed_item_quantity_in_cart`                                                |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectOrderAddressValidator`                                        | `sylius_api.validator.correct_order_address`                                                        |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator`                              | `sylius_api.validator.order_payment_method_eligibility`                                             |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ChosenPaymentMethodEligibilityValidator`                             | `sylius_api.validator.chosen_payment_method_eligibility`                                            |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\CanPaymentMethodBeChangedValidator`                                  | `sylius_api.validator.can_payment_method_be_changed`                                                |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\CorrectChangeShopUserConfirmPasswordValidator`                       | `sylius_api.validator.correct_change_shop_user_confirm_password`                                    |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ConfirmResetPasswordValidator`                                       | `sylius_api.validator.confirm_reset_password`                                                       |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\PromotionCouponEligibilityValidator`                                 | `sylius_api.validator.promotion_coupon_eligibility`                                                 |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ShipmentAlreadyShippedValidator`                                     | `sylius_api.validator.shipment_already_shipped`                                                     |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenExistsValidator`                           | `sylius_api.validator.shop_user_reset_password_token_exists`                                        |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserResetPasswordTokenNotExpiredValidator`                       | `sylius_api.validator.shop_user_reset_password_token_not_expired`                                   |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\ShopUserNotVerifiedValidator`                                        | `sylius_api.validator.shop_user_not_verified`                                                       |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\SingleValueForProductVariantOptionValidator`                         | `sylius_api.validator.single_value_for_product_variant_option`                                      |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\UniqueReviewerEmailValidator`                                        | `sylius_api.validator.unique_reviewer_email`                                                        |
    | `Sylius\Bundle\ApiBundle\Validator\Constraints\AdminResetPasswordTokenNonExpiredValidator`                          | `sylius_api.validator.admin_reset_password_token_non_expired`                                       |
    | `sylius.validator.order_address_requirement`                                                                        | `sylius_api.validator.order_address_requirement`                                                    |
    | `Sylius\Bundle\ApiBundle\PropertyInfo\Extractor\EmptyPropertyListExtractor`                                         | `sylius_api.extractor.property_info.empty_property_list`                                            |
    | `Sylius\Bundle\ApiBundle\SectionResolver\AdminApiUriBasedSectionResolver`                                           | `sylius_api.section_resolver.admin_api_uri_based`                                                   |
    | `Sylius\Bundle\ApiBundle\SectionResolver\ShopApiUriBasedSectionResolver`                                            | `sylius_api.section_resolver.shop_api_uri_based`                                                    |
    | `Sylius\Bundle\ApiBundle\EventListener\ApiCartBlamerListener`                                                       | `sylius_api.listener.api_cart_blamer`                                                               |
    | `sylius.listener.api_authentication_success_listener`                                                               | `sylius_api.listener.authentication_success`                                                        |
    | `sylius.listener.admin_api_authentication_success_listener`                                                         | `sylius_api.listener.admin_authentication_success`                                                  |
    | `Sylius\Bundle\ApiBundle\OpenApi\Factory\OpenApiFactory`                                                            | `sylius_api.open_api.factory`                                                                       |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AcceptLanguageHeaderDocumentationModifier`                           | `sylius_api.open_api.documentation_modifier.accept_language_header`                                 |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AdministratorDocumentationModifier`                                  | `sylius_api.open_api.documentation_modifier.administrator`                                          |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AttributeTypeDocumentationModifier`                                  | `sylius_api.open_api.documentation_modifier.attribute_type`                                         |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductDocumentationModifier`                                        | `sylius_api.open_api.documentation_modifier.product`                                                |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ImageDocumentationModifier`                                          | `sylius_api.open_api.documentation_modifier.image`                                                  |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductReviewDocumentationModifier`                                  | `sylius_api.open_api.documentation_modifier.product_review`                                         |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductSlugDocumentationModifier`                                    | `sylius_api.open_api.documentation_modifier.product_slug`                                           |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ProductVariantDocumentationModifier`                                 | `sylius_api.open_api.documentation_modifier.product_variant`                                        |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\ShippingMethodDocumentationModifier`                                 | `sylius_api.open_api.documentation_modifier.shipping_method`                                        |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\CustomerDocumentationModifier`                                       | `sylius_api.open_api.documentation_modifier.customer`                                               |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\StatisticsDocumentationModifier`                                     | `sylius_api.open_api.documentation_modifier.statistics`                                             |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\PromotionDocumentationModifier`                                      | `sylius_api.open_api.documentation_modifier.promotion`                                              |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\OrderAdjustmentsTypeDocumentationModifier`                           | `sylius_api.open_api.documentation_modifier.order_adjustments`                                      |
    | `Sylius\Bundle\ApiBundle\OpenApi\Documentation\AddressLogEntryDocumentationModifier`                                | `sylius_api.open_api.documentation_modifier.address_log_entry`                                      |
    | **AttributeBundle**                                                                                                 |                                                                                                     |
    | `sylius.form.type.attribute_type.select.choices_collection`                                                         | `sylius.form.type.attribute_type.configuration.select_attribute_choices_collection`                 |
    | `sylius.attribute_type.select.value.translations`                                                                   | `sylius.form.type.attribute_type.configuration.select_attribute_value_translations`                 |
    | `sylius.validator.valid_text_attribute`                                                                             | `sylius.validator.valid_text_attribute_configuration`                                               |
    | `sylius.validator.valid_select_attribute`                                                                           | `sylius.validator.valid_select_attribute_configuration`                                             |
    | **ChannelBundle**                                                                                                   |                                                                                                     |
    | `sylius.channel_collector`                                                                                          | `sylius.collector.channel`                                                                          |
    | **CoreBundle**                                                                                                      |                                                                                                     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator`                                 | `sylius.calculator.catalog_promotion.fixed_discount_price`                                          |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator`                            | `sylius.calculator.catalog_promotion.percentage_discount_price`                                     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityChecker`                              | `sylius.checker.catalog_promotion_eligibility`                                                      |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker`                                 | `sylius.checker.catalog_promotion.in_for_product_scope_variant`                                     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker`                                  | `sylius.checker.catalog_promotion.in_for_taxons_scope_variant`                                      |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker`                                | `sylius.checker.catalog_promotion.in_for_variants_scope_variant`                                    |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\ApplyCatalogPromotionsOnVariantsHandler`                  | `sylius.command_handler.catalog_promotion.apply_variants`                                           |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\DisableCatalogPromotionHandler`                           | `sylius.command_handler.catalog_promotion.disable`                                                  |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveCatalogPromotionHandler`                            | `sylius.command_handler.catalog_promotion.remove`                                                   |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\UpdateCatalogPromotionStateHandler`                       | `sylius.command_handler.catalog_promotion.update_state`                                             |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\CatalogPromotionEventListener`                             | `sylius.listener.catalog_promotion`                                                                 |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductEventListener`                                      | `sylius.listener.catalog_promotion.product`                                                         |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductVariantEventListener`                               | `sylius.listener.catalog_promotion.product_variant`                                                 |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionCreatedListener`                                | `sylius.listener.catalog_promotion.created`                                                         |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionUpdatedListener`                                | `sylius.listener.catalog_promotion.updated`                                                         |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionEndedListener`                                  | `sylius.listener.catalog_promotion.ended`                                                           |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionStateChangedListener`                           | `sylius.listener.catalog_promotion.state_changed`                                                   |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductCreatedListener`                                         | `sylius.listener.catalog_promotion.product_created`                                                 |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductUpdatedListener`                                         | `sylius.listener.catalog_promotion.product_updated`                                                 |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantCreatedListener`                                  | `sylius.listener.catalog_promotion.product_variant_created`                                         |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantUpdatedListener`                                  | `sylius.listener.catalog_promotion.product_variant_updated`                                         |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderNumberListener`                                   | `sylius.listener.workflow.order.assign_order_number`                                                |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderTokenListener`                                    | `sylius.listener.workflow.order.assign_order_token`                                                 |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreatePaymentListener`                                       | `sylius.listener.workflow.order.create_payment`                                                     |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreateShipmentListener`                                      | `sylius.listener.workflow.order.create_shipment`                                                    |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\DecrementPromotionUsagesListener`                            | `sylius.listener.workflow.order.decrement_promotion_usages`                                         |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\IncrementPromotionUsagesListener`                            | `sylius.listener.workflow.order.increment_promotion_usages`                                         |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\HoldInventoryListener`                                       | `sylius.listener.workflow.order.hold_inventory`                                                     |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\GiveBackInventoryListener`                                   | `sylius.listener.workflow.order.give_back_inventory`                                                |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderPaymentListener`                                 | `sylius.listener.workflow.order.request_order_payment`                                              |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderShippingListener`                                | `sylius.listener.workflow.order.request_order_shipping`                                             |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SaveCustomerAddressesListener`                               | `sylius.listener.workflow.order.save_customer_addresses`                                            |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SetImmutableNamesListener`                                   | `sylius.listener.workflow.order.set_immutable_names`                                                |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderPaymentListener`                                  | `sylius.listener.workflow.order.cancel_order_payment`                                               |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderShippingListener`                                 | `sylius.listener.workflow.order.cancel_order_shipping`                                              |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelPaymentListener`                                       | `sylius.listener.workflow.order.cancel_payment`                                                     |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelShipmentListener`                                      | `sylius.listener.workflow.order.cancel_shipment`                                                    |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ProcessCartListener`                                 | `sylius.listener.workflow.order_checkout.process_cart`                                              |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ApplyCreateTransitionOnOrderListener`                | `sylius.listener.workflow.order_checkout.apply_create_transition_on_order`                          |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\SaveCheckoutCompletionDateListener`                  | `sylius.listener.workflow.order_checkout.save_checkout_completion_date`                             |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderCheckoutStateListener`                   | `sylius.listener.workflow.order_checkout.resolve_order_checkout_state`                              |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderPaymentStateListener`                    | `sylius.listener.workflow.order_checkout.resolve_order_payment_state`                               |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderShippingStateListener`                   | `sylius.listener.workflow.order_checkout.resolve_order_shipping_state`                              |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\SellOrderInventoryListener`                           | `sylius.listener.workflow.order_payment.sell_order_inventory`                                       |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\ResolveOrderStateListener`                            | `sylius.listener.workflow.order_payment.resolve_order_state`                                        |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping\ResolveOrderStateListener`                           | `sylius.listener.workflow.order_shipping.resolve_order_state`                                       |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ProcessOrderListener`                                      | `sylius.listener.workflow.payment.process_order`                                                    |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ResolveOrderPaymentStateListener`                          | `sylius.listener.workflow.payment.resolve_order_payment_state`                                      |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\AssignShippingDateListener`                               | `sylius.listener.workflow.shipment.assign_shipping_date`                                            |
    | `Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\ResolveOrderShipmentStateListener`                        | `sylius.listener.workflow.shipment.resolve_order_shipment_state`                                    |
    | `Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler\ApplyLowestPriceOnChannelPricingsHandler`                     | `sylius.command_handler.price_history.apply_lowest_price_on_channel_pricings`                       |
    | `Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\CreateLogEntryOnPriceChangeObserver`                          | `sylius.entity_observer.price_history.create_log_entry_on_price_change`                             |
    | `Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelChangeObserver`                   | `sylius.entity_observer.price_history.process_lowest_prices_on_channel_change`                      |
    | `Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver` | `sylius.entity_observer.price_history.process_lowest_prices_on_channel_price_history_config_change` |
    | `Sylius\Bundle\CoreBundle\PriceHistory\EventListener\OnFlushEntityObserverListener`                                 | `sylius.listener.price_history.on_flush_entity_observer`                                            |
    | `Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener`                           | `sylius.listener.price_history.channel_pricing_log_entry`                                           |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\ExclusiveCriteria`                           | `sylius.discount_application_criteria.catalog_promotion.exclusive`                                  |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\MinimumPriceCriteria`                        | `sylius.discount_application_criteria.catalog_promotion.minimum_price`                              |
    | `sylius.promotion_coupon_channels_eligibility_checker`                                                              | `sylius.checker.promotion_coupon.channel_eligibility`                                               |
    | `sylius.form.type.checkout_address`                                                                                 | `sylius.form.type.checkout.address`                                                                 |
    | `sylius.form.type.checkout_select_shipping`                                                                         | `sylius.form.type.checkout.select_shipping`                                                         |
    | `sylius.form.type.checkout_shipment`                                                                                | `sylius.form.type.checkout.shipment`                                                                |
    | `sylius.form.type.checkout_select_payment`                                                                          | `sylius.form.type.checkout.select_payment`                                                          |
    | `sylius.form.type.checkout_payment`                                                                                 | `sylius.form.type.checkout.payment`                                                                 |
    | `sylius.form.type.checkout_complete`                                                                                | `sylius.form.type.checkout.complete`                                                                |
    | `Sylius\Bundle\CoreBundle\Console\Command\CancelUnpaidOrdersCommand`                                                | `sylius.console.command.cancel_unpaid_orders`                                                       |
    | `Sylius\Bundle\CoreBundle\Console\Command\CheckRequirementsCommand`                                                 | `sylius.console.command.check_requirements`                                                         |
    | `Sylius\Bundle\CoreBundle\PriceHistory\Console\Command\ClearPriceHistoryCommand`                                    | `sylius.console.command.price_history.clear`                                                        |
    | `Sylius\Bundle\CoreBundle\Console\Command\InstallAssetsCommand`                                                     | `sylius.console.command.install_assets`                                                             |
    | `Sylius\Bundle\CoreBundle\Console\Command\InstallCommand`                                                           | `sylius.console.command.install`                                                                    |
    | `Sylius\Bundle\CoreBundle\Console\Command\InstallDatabaseCommand`                                                   | `sylius.console.command.install_database`                                                           |
    | `Sylius\Bundle\CoreBundle\Console\Command\InstallSampleDataCommand`                                                 | `sylius.console.command.install_sample_data`                                                        |
    | `Sylius\Bundle\CoreBundle\Console\Command\SetupCommand`                                                             | `sylius.console.command.setup`                                                                      |
    | `Sylius\Bundle\CoreBundle\Console\Command\InformAboutGUSCommand`                                                    | `sylius.console.command.inform_about_gus`                                                           |
    | `Sylius\Bundle\CoreBundle\Console\Command\JwtConfigurationCommand`                                                  | `sylius.console.command.jwt_configuration`                                                          |
    | `Sylius\Bundle\CoreBundle\Console\Command\ShowPlusInfoCommand`                                                      | `sylius.console.command.show_plus_info`                                                             |
    | `sylius.locale_provider.channel_based`                                                                              | `sylius.provider.locale.channel_based`                                                              |
    | `Sylius\Bundle\CoreBundle\Fixture\CatalogPromotionFixture`                                                          | `sylius.fixture.catalog_promotion`                                                                  |
    | `Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionExampleFactory`                                           | `sylius.fixture.example_factory.catalog_promotion`                                                  |
    | `Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionScopeExampleFactory`                                      | `sylius.fixture.example_factory.catalog_promotion_scope`                                            |
    | `Sylius\Bundle\CoreBundle\Fixture\Factory\CatalogPromotionActionExampleFactory`                                     | `sylius.fixture.example_factory.catalog_promotion_action`                                           |
    | `sylius_fixtures.listener.catalog_promotion_executor`                                                               | `sylius.fixture.listener.catalog_promotion_executor`                                                |
    | `Sylius\Bundle\CoreBundle\Fixture\Listener\ImagesPurgerListener`                                                    | `sylius.fixture.listener.images_purger`                                                             |
    | `Sylius\Bundle\CoreBundle\Form\Extension\CatalogPromotionTypeExtension`                                             | `sylius.form.extension.type.catalog_promotion`                                                      |
    | `Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionAction\ChannelBasedFixedDiscountActionConfigurationType`        | `sylius.form.type.catalog_promotion_action.channel_based_fixed_discount_action_configuration`       |
    | `sylius.form.type.for_products_scope`                                                                               | `sylius.form.type.catalog_promotion_scope.for_products_scope_configuration`                         |
    | `sylius.form.type.for_taxons_scope`                                                                                 | `sylius.form.type.catalog_promotion_scope.for_taxons_scope_configuration`                           |
    | `sylius.form.type.for_variants_scope`                                                                               | `sylius.form.type.catalog_promotion_scope.for_variants_scope_configuration`                         |
    | `sylius.form.type.customer_guest`                                                                                   | `sylius.form.type.customer.guest`                                                                   |
    | `sylius.form.type.customer_checkout_guest`                                                                          | `sylius.form.type.customer.checkout_guest`                                                          |
    | `sylius.form.type.customer_simple_registration`                                                                     | `sylius.form.type.customer.simple_registration`                                                     |
    | `sylius.form.type.customer_registration`                                                                            | `sylius.form.type.customer.registration`                                                            |
    | `sylius.form.type.add_to_cart`                                                                                      | `sylius.form.type.order.add_to_cart`                                                                |
    | `sylius.form.type.channel_pricing`                                                                                  | `sylius.form.type.product.channel_pricing`                                                          |
    | `sylius.form.type.channel_based_shipping_calculator.flat_rate`                                                      | `sylius.form.type.shipping.calculator.channel_based_flat_rate_configuration`                        |
    | `sylius.form.type.channel_based_shipping_calculator.per_unit_rate`                                                  | `sylius.form.type.shipping.calculator.channel_based_per_unit_rate_configuration`                    |
    | `sylius.form.type.autocomplete_product_taxon_choice`                                                                | `sylius.form.type.product_taxon_autocomplete_choice`                                                |
    | `sylius.installer.checker.command_directory`                                                                        | `sylius.checker.installer.command_directory`                                                        |
    | `sylius.installer.checker.sylius_requirements`                                                                      | `sylius.checker.installer.sylius_requirements`                                                      |
    | `sylius.commands_provider.database_setup`                                                                           | `sylius.provider.installer.database_setup_commands`                                                 |
    | `sylius.setup.currency`                                                                                             | `sylius.setup.installer.currency`                                                                   |
    | `sylius.setup.locale`                                                                                               | `sylius.setup.installer.locale`                                                                     |
    | `sylius.setup.channel`                                                                                              | `sylius.setup.installer.channel`                                                                    |
    | `sylius.requirements`                                                                                               | `sylius.requirements.installer.sylius`                                                              |
    | `sylius.listener.channel`                                                                                           | `sylius.listener.channel_deletion`                                                                  |
    | `sylius.listener.default_username`                                                                                  | `sylius.listener.default_username_orm`                                                              |
    | `Sylius\Bundle\CoreBundle\EventListener\LocaleAwareListener`                                                        | `sylius.listener.locale_aware`                                                                      |
    | `Sylius\Bundle\CoreBundle\EventListener\XFrameOptionsSubscriber`                                                    | `sylius.event_subscriber.x_frame_options`                                                           |
    | `Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener`                                                 | `sylius.listener.payment_pre_complete`                                                              |
    | `Sylius\Bundle\CoreBundle\EventListener\ProductDeletionListener`                                                    | `sylius.listener.product_deletion`                                                                  |
    | `Sylius\Bundle\CoreBundle\EventListener\PostgreSQLDefaultSchemaListener`                                            | `sylius.listener.postgre_sql_default_schema`                                                        |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOptionsMapProvider`                                 | `sylius.provider.product_variant_map.options`                                                       |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantPriceMapProvider`                                   | `sylius.provider.product_variant_map.price`                                                         |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOriginalPriceMapProvider`                           | `sylius.provider.product_variant_map.original_price`                                                |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantAppliedPromotionsMapProvider`                       | `sylius.provider.product_variant_map.applied_promotions`                                            |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantLowestPriceMapProvider`                             | `sylius.provider.product_variant_map.lowest_price`                                                  |
    | `sylius.promotion_rule_checker.customer_group`                                                                      | `sylius.checker.promotion_rule.customer_group`                                                      |
    | `sylius.promotion_rule_checker.nth_order`                                                                           | `sylius.checker.promotion_rule.nth_order`                                                           |
    | `sylius.promotion_rule_checker.shipping_country`                                                                    | `sylius.checker.promotion_rule.shipping_country`                                                    |
    | `sylius.promotion_rule_checker.has_taxon`                                                                           | `sylius.checker.promotion_rule.has_taxon`                                                           |
    | `sylius.promotion_rule_checker.total_of_items_from_taxon`                                                           | `sylius.checker.promotion_rule.total_of_items_from_taxon`                                           |
    | `sylius.promotion_rule_checker.contains_product`                                                                    | `sylius.checker.promotion_rule.contains_product`                                                    |
    | `sylius.promotion_rule_checker.item_total`                                                                          | `sylius.checker.promotion_rule.item_total`                                                          |
    | `sylius.promotion_rule_checker.cart_quantity`                                                                       | `sylius.checker.promotion_rule.cart_quantity`                                                       |
    | `sylius.promotion_action.fixed_discount`                                                                            | `sylius.command.promotion_action.fixed_discount`                                                    |
    | `sylius.promotion_action.unit_fixed_discount`                                                                       | `sylius.command.promotion_action.unit_fixed_discount`                                               |
    | `sylius.promotion_action.percentage_discount`                                                                       | `sylius.command.promotion_action.percentage_discount`                                               |
    | `sylius.promotion_action.unit_percentage_discount`                                                                  | `sylius.command.promotion_action.unit_percentage_discount`                                          |
    | `sylius.promotion_action.shipping_percentage_discount`                                                              | `sylius.command.promotion_action.shipping_percentage_discount`                                      |
    | `sylius.promotion.eligibility_checker.promotion_coupon_per_customer_usage_limit`                                    | `sylius.checker.promotion.promotion_coupon_per_customer_usage_limit_eligibility`                    |
    | `sylius.promotion_filter.taxon`                                                                                     | `sylius.filter.promotion.taxon`                                                                     |
    | `sylius.promotion_filter.product`                                                                                   | `sylius.filter.promotion.product`                                                                   |
    | `sylius.promotion_filter.price_range`                                                                               | `sylius.filter.promotion.price_range`                                                               |
    | `sylius.promotion.units_promotion_adjustments_applicator`                                                           | `sylius.applicator.promotion.units_adjustments`                                                     |
    | `sylius.promotion_usage_modifier`                                                                                   | `sylius.modifier.promotion.order_usage`                                                             |
    | `sylius.promotion_rule_updater.has_taxon`                                                                           | `sylius.updater.promotion_rule.has_taxon`                                                           |
    | `sylius.provider.channel_based_default_zone_provider`                                                               | `sylius.provider.channel_based_default_zone`                                                        |
    | `sylius.translation_locale_provider.admin`                                                                          | `sylius.provider.translation_locale.admin`                                                          |
    | `sylius.orders_totals_provider.day`                                                                                 | `sylius.provider.statistics.orders_totals.day`                                                      |
    | `sylius.orders_totals_provider.month`                                                                               | `sylius.provider.statistics.orders_totals.month`                                                    |
    | `sylius.orders_totals_provider.year`                                                                                | `sylius.provider.statistics.orders_totals.year`                                                     |
    | `sylius.shipping_method_rule_checker.order_total_greater_than_or_equal`                                             | `sylius.checker.shipping_method_rule.order_total_greater_than_or_equal`                             |
    | `sylius.shipping_method_rule_checker.order_total_less_than_or_equal`                                                | `sylius.checker.shipping_method_rule.order_total_less_than_or_equal`                                |
    | `sylius.state_resolver.order_checkout`                                                                              | `sylius.state_resolver.checkout`                                                                    |
    | `sylius.taxation.order_shipment_taxes_applicator`                                                                   | `sylius.applicator.taxation.order_shipment`                                                         |
    | `sylius.taxation.order_items_taxes_applicator`                                                                      | `sylius.applicator.taxation.order_items`                                                            |
    | `sylius.taxation.order_item_units_taxes_applicator`                                                                 | `sylius.applicator.taxation.order_item_units`                                                       |
    | `sylius.taxation.order_items_based_strategy`                                                                        | `sylius.strategy.taxation.tax_calculation.order_items_based`                                        |
    | `sylius.taxation.order_item_units_based_strategy`                                                                   | `sylius.strategy.taxation.tax_calculation.order_item_units_based`                                   |
    | `sylius.validator.unique.registered_user`                                                                           | `sylius.validator.registered_user`                                                                  |
    | `sylius.validator.shipping_method_integrity`                                                                        | `sylius.validator.order_shipping_method_eligibility`                                                |
    | `sylius.validator.payment_method_integrity`                                                                         | `sylius.validator.order_payment_method_eligibility`                                                 |
    | `sylius.validator.product_integrity`                                                                                | `sylius.validator.order_product_eligibility`                                                        |
    | `sylius.validator.order_confirmation_with_valid_order_state`                                                        | `sylius.validator.resend_order_confirmation_email_with_valid_order_state`                           |
    | `sylius.validator.shipment_confirmation_with_valid_order_state`                                                     | `sylius.validator.resend_shipment_confirmation_email_with_valid_order_state`                        |
    | `Sylius\Bundle\CoreBundle\Validator\Constraints\MaxIntegerValidator`                                                | `sylius.validator.max_integer`                                                                      |
    | `sylius.integer_distributor`                                                                                        | `sylius.distributor.integer`                                                                        |
    | `sylius.proportional_integer_distributor`                                                                           | `sylius.distributor.proportional_integer`                                                           |
    | `sylius.invoice_number_generator`                                                                                   | `sylius.generator.invoice_number.id_based`                                                          |
    | `sylius.image_uploader`                                                                                             | `sylius.uploader.image`                                                                             |
    | `Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter`                                               | `sylius.adapter.filesystem.flysystem`                                                               |
    | `Sylius\Bundle\CoreBundle\Collector\CartCollector`                                                                  | `sylius.collector.cart`                                                                             |
    | `sylius.shipping_methods_resolver.zones_and_channel_based`                                                          | `sylius.resolver.shipping_methods.zones_and_channel_based`                                          |
    | `sylius.payment_methods_resolver.channel_based`                                                                     | `sylius.resolver.payment_methods.channel_based`                                                     |
    | `sylius.payment_method_resolver.default`                                                                            | `sylius.resolver.payment_method.default`                                                            |
    | `sylius.taxation_address_resolver`                                                                                  | `sylius.resolver.taxation_address`                                                                  |
    | `sylius.inventory.order_item_availability_checker`                                                                  | `sylius.checker.inventory.order_item_availability`                                                  |
    | `sylius.inventory.order_inventory_operator`                                                                         | `sylius.operator.inventory.order_inventory`                                                         |
    | `sylius.custom_inventory.order_inventory_operator`                                                                  | `sylius.custom_operator.inventory.order_inventory`                                                  |
    | `Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension`                                                         | `sylius.twig.extension.product_variants_map`                                                        |
    | `sylius.unique_id_based_order_token_assigner`                                                                       | `sylius.assigner.order_token.unique_id_based`                                                       |
    | `sylius.customer_unique_address_adder`                                                                              | `sylius.adder.customer.unique_address`                                                              |
    | `sylius.customer_order_addresses_saver`                                                                             | `sylius.saver.customer.order_addresses`                                                             |
    | `sylius.order_item_quantity_modifier.limiting`                                                                      | `sylius.modifier.cart.limiting_order_item_quantity`                                                 |
    | `sylius.customer_ip_assigner`                                                                                       | `sylius.assigner.customer_id`                                                                       |
    | `sylius.section_resolver.uri_based_section_resolver`                                                                | `sylius.section_resolver.uri_based`                                                                 |
    | `sylius.reviewer_reviews_remover`                                                                                   | `sylius.remover.reviewer_reviews`                                                                   |
    | `sylius.unpaid_orders_state_updater`                                                                                | `sylius.updater.unpaid_orders_state`                                                                |
    | `sylius.order_payment_provider`                                                                                     | `sylius.provider.payment.order`                                                                     |
    | `sylius.customer_statistics_provider`                                                                               | `sylius.provider.statistics.customer`                                                               |
    | `sylius.order_item_names_setter`                                                                                    | `sylius.setter.order.item_names`                                                                    |
    | `sylius.user_password_resetter.admin`                                                                               | `sylius.resetter.user_password.admin`                                                               |
    | `sylius.user_password_resetter.shop`                                                                                | `sylius.resetter.user_password.shop`                                                                |
    | **CurrencyBundle**                                                                                                  |                                                                                                     |
    | `sylius.currency_converter`                                                                                         | `sylius.converter.currency`                                                                         |
    | `sylius.currency_name_converter`                                                                                    | `sylius.converter.currency_name`                                                                    |
    | **InventoryBundle**                                                                                                 |                                                                                                     |
    | `sylius.availability_checker.default`                                                                               | `sylius.checker.inventory.availability`                                                             |
    | **LocaleBundle**                                                                                                    |                                                                                                     |
    | `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext`                                                | `sylius.context.locale.request_header_based`                                                        |
    | `sylius.locale_collection_provider`                                                                                 | `sylius.provider.locale_collection`                                                                 |
    | `sylius.locale_collection_provider.cached`                                                                          | `sylius.provider.locale_collection.cached`                                                          |
    | `sylius.locale_provider`                                                                                            | `sylius.provider.locale`                                                                            |
    | `sylius.locale_converter`                                                                                           | `sylius.converter.locale`                                                                           |
    | `Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener`                                      | `sylius.doctrine.listener.locale_modification`                                                      |
    | **MoneyBundle**                                                                                                     |                                                                                                     |
    | `sylius.twig.extension.convert_amount`                                                                              | `sylius.twig.extension.convert_money`                                                               |
    | `sylius.twig.extension.money`                                                                                       | `sylius.twig.extension.format_money`                                                                |
    | `sylius.money_formatter`                                                                                            | `sylius.formatter.money`                                                                            |
    | **OrderBundle**                                                                                                     |
    | `sylius.order_modifier`                                                                                             | `sylius.modifier.order`                                                                             |
    | `sylius.order_item_quantity_modifier`                                                                               | `sylius.modifier.order_item_quantity`                                                               |
    | `sylius.order_number_assigner`                                                                                      | `sylius.number_assigner.order_number`                                                               |
    | `sylius.adjustments_aggregator`                                                                                     | `sylius.aggregator.adjustments_by_label`                                                            |
    | `sylius.expired_carts_remover`                                                                                      | `sylius.remover.expired_carts`                                                                      |
    | `sylius.sequential_order_number_generator`                                                                          | `sylius.number_generator.sequential_order`                                                          |
    | `Sylius\Bundle\OrderBundle\Console\Command\RemoveExpiredCartsCommand`                                               | `sylius.console.command.remove_expired_carts`                                                       |
    | **PaymentBundle**                                                                                                   |                                                                                                     |
    | `sylius.payment_methods_resolver`                                                                                   | `sylius.resolver.payment_methods`                                                                   |
    | `sylius.payment_methods_resolver.default`                                                                           | `sylius.resolver.payment_methods.default`                                                           |
    | **PayumBundle**                                                                                                     |                                                                                                     |
    | `sylius.payum_action.authorize_payment`                                                                             | `sylius_payum.action.authorize_payment`                                                             |
    | `sylius.payum_action.capture_payment`                                                                               | `sylius_payum.action.capture_payment`                                                               |
    | `sylius.payum_action.execute_same_request_with_payment_details`                                                     | `sylius_payum.action.execute_same_request_with_payment_details`                                     |
    | `sylius.payum_action.resolve_next_route`                                                                            | `sylius_payum.action.resolve_next_route`                                                            |
    | `sylius.payum_action.offline.convert_payment`                                                                       | `sylius_payum.action.offline.convert_payment`                                                       |
    | `sylius.payum_action.offline.status`                                                                                | `sylius_payum.action.offline.status`                                                                |
    | `sylius.payum_action.offline.resolve_next_route`                                                                    | `sylius_payum.action.offline.resolve_next_route`                                                    |
    | `sylius.payum_extension.update_payment_state`                                                                       | `sylius_payum.extension.update_payment_state`                                                       |
    | `sylius.factory.payum_get_status_action`                                                                            | `sylius_payum.factory.get_status`                                                                   |
    | `sylius.factory.payum_resolve_next_route`                                                                           | `sylius_payum.factory.resolve_next_route`                                                           |
    | `sylius.form.extension.type.gateway_config.crypted`                                                                 | `sylius_payum.form.extension.type.crypted_gateway_config`                                           |
    | `sylius.payment_description_provider`                                                                               | `sylius_payum.provider.payment_description`                                                         |
    | `sylius.payum.http_client`                                                                                          | `sylius_payum.http_client`                                                                          |
    | **ProductBundle**                                                                                                   |
    | `sylius.form.type.sylius_product_associations`                                                                      | `sylius.form.type.product_associations`                                                             |
    | `sylius.form.event_subscriber.product_variant_generator`                                                            | `sylius.form.event_subscriber.generate_product_variants`                                            |
    | `Sylius\Bundle\ProductBundle\Validator\ProductVariantOptionValuesConfigurationValidator`                            | `sylius.validator.product_variant_option_values_configuration`                                      |
    | `sylius.validator.product_code_uniqueness`                                                                          | `sylius.validator.unique_simple_product_code`                                                       |
    | `sylius.product_variant_resolver.default`                                                                           | `sylius.resolver.product_variant.default`                                                           |
    | `sylius.available_product_option_values_resolver`                                                                   | `sylius.resolver.available_product_option_values`                                                   |
    | **PromotionBundle**                                                                                                 |                                                                                                     |
    | `Sylius\Bundle\PromotionBundle\Console\Command\GenerateCouponsCommand`                                              | `sylius.console.command.generate_coupons`                                                           |
    | `sylius.promotion_coupon_duration_eligibility_checker`                                                              | `sylius.checker.promotion_coupon.duration_eligibility`                                              |
    | `sylius.promotion_coupon_usage_limit_eligibility_checker`                                                           | `sylius.checker.promotion_coupon.usage_limit_eligibility`                                           |
    | `sylius.promotion_coupon_eligibility_checker`                                                                       | `sylius.checker.promotion_coupon_eligibility`                                                       |
    | `sylius.promotion_duration_eligibility_checker`                                                                     | `sylius.checker.promotion.duration_eligibility`                                                     |
    | `sylius.promotion_usage_limit_eligibility_checker`                                                                  | `sylius.checker.promotion.usage_limit_eligibility`                                                  |
    | `sylius.promotion_subject_coupon_eligibility_checker`                                                               | `sylius.checker.promotion.subject_coupon_eligibility`                                               |
    | `sylius.promotion_rules_eligibility_checker`                                                                        | `sylius.checker.promotion.rules_eligibility`                                                        |
    | `sylius.promotion_archival_eligibility_checker`                                                                     | `sylius.checker.promotion.archival_eligibility`                                                     |
    | `sylius.promotion_eligibility_checker`                                                                              | `sylius.checker.promotion_eligibility`                                                              |
    | `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType`                                                      | `sylius.form.type.catalog_promotion`                                                                |
    | `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType`                                                 | `sylius.form.type.catalog_promotion_scope`                                                          |
    | `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`          | `sylius.form.type.catalog_promotion_action.percentage_discount_action_configuration`                |
    | `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType`                                                | `sylius.form.type.catalog_promotion_action`                                                         |
    | `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionTranslationType`                                           | `sylius.form.type.catalog_promotion_translation`                                                    |
    | `Sylius\Bundle\PromotionBundle\Form\Type\PromotionTranslationType`                                                  | `sylius.form.type.promotion_translation`                                                            |
    | `sylius.form.type.promotion_action.collection`                                                                      | `sylius.form.type.promotion_action_collection`                                                      |
    | `sylius.form.type.promotion_rule.collection`                                                                        | `sylius.form.type.promotion_rule_collection`                                                        |
    | `sylius.validator.date_range`                                                                                       | `sylius.validator.promotion_date_range`                                                             |
    | `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionGroupValidator`                                      | `sylius.validator.catalog_promotion_action_group`                                                   |
    | `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionTypeValidator`                                       | `sylius.validator.catalog_promotion_action_type`                                                    |
    | `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeGroupValidator`                                       | `sylius.validator.catalog_promotion_scope_group`                                                    |
    | `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeTypeValidator`                                        | `sylius.validator.catalog_promotion_scope_type`                                                     |
    | `Sylius\Bundle\PromotionBundle\Validator\PromotionActionGroupValidator`                                             | `sylius.validator.promotion_action_group`                                                           |
    | `Sylius\Bundle\PromotionBundle\Validator\PromotionActionTypeValidator`                                              | `sylius.validator.promotion_action_type`                                                            |
    | `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator`                                               | `sylius.validator.promotion_role_group`                                                             |
    | `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleTypeValidator`                                                | `sylius.validator.promotion_role_type`                                                              |
    | `Sylius\Bundle\PromotionBundle\Validator\PromotionNotCouponBasedValidator`                                          | `sylius.validator.promotion_not_coupon_based`                                                       |
    | `sylius.promotion_processor`                                                                                        | `sylius.processor.promotion`                                                                        |
    | `sylius.promotion_applicator`                                                                                       | `sylius.action.applicator.promotion`                                                                |
    | `sylius.registry_promotion_rule_checker`                                                                            | `sylius.registry.promotion.rule_checker`                                                            |
    | `sylius.registry_promotion_action`                                                                                  | `sylius.registry.promotion_action`                                                                  |
    | `sylius.active_promotions_provider`                                                                                 | `sylius.provider.active_promotions`                                                                 |
    | `sylius.promotion_coupon_generator`                                                                                 | `sylius.generator.promotion_coupon`                                                                 |
    | `sylius.promotion_coupon_generator.percentage_policy`                                                               | `sylius.generator.percentage_generation_policy`                                                     |
    | **ReviewBundle**                                                                                                    |                                                                                                     |
    | `sylius.average_rating_calculator`                                                                                  | `sylius.calculator.average_rating`                                                                  |
    | `sylius.%s_review.average_rating_updater`                                                                           | `sylius.updater.%s_review.average_rating`                                                           |
    | **Note: `%s` refers to the entity names associated with reviews (e.g., `product`, etc.).**                          |                                                                                                     |
    | **ShippingBundle**                                                                                                  |                                                                                                     |
    | `sylius.category_requirement_shipping_method_eligibility_checker`                                                   | `sylius.checker.shipping_method.category_requirement_eligibility`                                   |
    | `sylius.shipping_method_rules_shipping_method_eligibility_checker`                                                  | `sylius.checker.shipping_method.rules_eligibility`                                                  |
    | `sylius.shipping_method_eligibility_checker`                                                                        | `sylius.checker.shipping_method_eligibility`                                                        |
    | `sylius.form.type.shipping_method_rule.collection`                                                                  | `sylius.form.type.shipping_method_rule_collection`                                                  |
    | `Sylius\Bundle\ShippingBundle\Validator\ShippingMethodCalculatorExistsValidator`                                    | `sylius.validator.shipping_method_calculator_exists`                                                |
    | `Sylius\Bundle\ShippingBundle\Validator\ShippingMethodRuleValidator`                                                | `sylius.validator.shipping_method_rule`                                                             |
    | `Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator`                 | `sylius.validator.groups_generator.shipping_method_configuration`                                   |
    | `sylius.shipping_methods_resolver`                                                                                  | `sylius.resolver.shipping_methods`                                                                  |
    | `sylius.shipping_methods_resolver.default`                                                                          | `sylius.resolver.shipping_methods.default`                                                          |
    | `sylius.shipping_method_resolver.default`                                                                           | `sylius.resolver.shipping_method.default`                                                           |
    | `sylius.shipping_calculator`                                                                                        | `sylius.calculator.shipping`                                                                        |
    | `sylius.shipping_calculator.flat_rate`                                                                              | `sylius.calculator.shipping.flat_rate`                                                              |
    | `sylius.shipping_calculator.per_unit_rate`                                                                          | `sylius.calculator.shipping.per_unit_rate`                                                          |
    | `sylius.shipping_date_assigner`                                                                                     | `sylius.assigner.shipping_date`                                                                     |
    | `sylius.shipping_method_rule_checker.total_weight_greater_than_or_equal`                                            | `sylius.checker.shipping_method_rule.total_weight_greater_than_or_equal`                            |
    | `sylius.shipping_method_rule_checker.total_weight_less_than_or_equal`                                               | `sylius.checker.shipping_method_rule.total_weight_less_than_or_equal`                               |
    | **ShopBundle**                                                                                                      |                                                                                                     |
    | `sylius.shop.locale_switcher`                                                                                       | `sylius_shop.locale_switcher`                                                                       |
    | `sylius.storage.locale`                                                                                             | `sylius_shop.storage.locale`                                                                        |
    | `sylius.context.locale.storage_based`                                                                               | `sylius_shop.context.locale.storage_based`                                                          |
    | `sylius.shop.locale_stripping_router`                                                                               | `sylius_shop.router.locale_stripping`                                                               |
    | `sylius.listener.non_channel_request_locale`                                                                        | `sylius_shop.listener.non_channel_locale`                                                           |
    | `sylius.controller.shop.contact`                                                                                    | `sylius_shop.controller.contact`                                                                    |
    | `sylius.controller.shop.currency_switch`                                                                            | `sylius_shop.controller.currency_switch`                                                            |
    | `sylius.controller.shop.locale_switch`                                                                              | `sylius_shop.controller.locale_switch`                                                              |
    | `sylius.controller.shop.register_thank_you`                                                                         | `sylius_shop.controller.register_thank_you`                                                         |
    | `sylius.mailer.contact_email_manager.shop`                                                                          | `sylius_shop.mailer.contact_email_manager`                                                          |
    | `sylius.mailer.order_email_manager.shop`                                                                            | `sylius_shop.mailer.order_email_manager`                                                            |
    | `sylius.listener.shop_cart_blamer`                                                                                  | `sylius_shop.listener.shop_cart_blamer`                                                             |
    | `sylius.listener.email_updater`                                                                                     | `sylius_shop.listener.customer_email_updater`                                                       |
    | `sylius.listener.shop_customer_account_sub_section_cache_control_subscriber`                                        | `sylius_shop.event_subscriber.shop_customer_account_sub_section_cache_control`                      |
    | `sylius.listener.order_customer_ip`                                                                                 | `sylius_shop.listener.order_customer_ip`                                                            |
    | `sylius.listener.order_complete`                                                                                    | `sylius_shop.listener.order_complete`                                                               |
    | `sylius.listener.user_registration`                                                                                 | `sylius_shop.listener.user_registration`                                                            |
    | `sylius.listener.order_integrity_checker`                                                                           | `sylius_shop.listener.order_integrity_checker`                                                      |
    | `sylius.order_locale_assigner`                                                                                      | `sylius_shop.listener.order_locale_assigner`                                                        |
    | `sylius.listener.session_cart`                                                                                      | `sylius_shop.event_subscriber.session_cart`                                                         |
    | `sylius.listener.user_cart_recalculation`                                                                           | `sylius_shop.listener.user_cart_recalculation`                                                      |
    | `sylius.listener.user_impersonated`                                                                                 | `sylius_shop.listener.user_impersonated`                                                            |
    | `sylius.shop.menu_builder.account`                                                                                  | `sylius_shop.menu_builder.account`                                                                  |
    | `sylius.twig.extension.original_price_to_display`                                                                   | `sylius_shop.twig.extension.order_item_original_price_to_display`                                   |
    | `Sylius\Bundle\ShopBundle\Twig\OrderPaymentsExtension`                                                              | `sylius_shop.twig.extension.order_payments`                                                         |
    | `sylius.section_resolver.shop_uri_based_section_resolver`                                                           | `sylius_shop.section_resolver.shop_uri_based`                                                       |
    | `sylius.context.cart.session_and_channel_based`                                                                     | `sylius_shop.context.cart.session_and_channel_based`                                                |
    | `sylius.storage.cart_session`                                                                                       | `sylius_shop.storage.cart_session`                                                                  |
    | `sylius.grid_filter.shop_string`                                                                                    | `sylius_shop.grid_filter.string`                                                                    |
    | **TaxationBundle**                                                                                                  |                                                                                                     |
    | `sylius.tax_rate_resolver`                                                                                          | `sylius.resolver.tax_rate`                                                                          |
    | `sylius.tax_rate_date_eligibility_checker`                                                                          | `sylius.checker.tax_rate_date_eligibility`                                                          |
    | **TaxonomyBundle**                                                                                                  |                                                                                                     |
    | `sylius.doctrine.odm.mongodb.unitOfWork`                                                                            | `sylius.doctrine.odm.mongodb.unit_of_work`                                                          |
    | **UiBundle**                                                                                                        |                                                                                                     |
    | `Sylius\Bundle\UiBundle\Twig\RedirectPathExtension`                                                                 | `sylius.twig.extension.redirect_path`                                                               |
    | **UserBundle**                                                                                                      |                                                                                                     |
    | `Sylius\Bundle\UserBundle\Console\Command\DemoteUserCommand`                                                        | `sylius.console.command.demote_user`                                                                |
    | `Sylius\Bundle\UserBundle\Console\Command\PromoteUserCommand`                                                       | `sylius.console.command.promote_user`                                                               |
    | `sylius.listener.user_mailer_listener`                                                                              | `sylius.listener.user_mailer`                                                                       |

   The old service IDs are now deprecated and will be removed in Sylius 2.0. Please update your service references accordingly to ensure compatibility with Sylius 2.0.

1. For the following services, new aliases have been added in Sylius 1.14.
   These aliases will become the primary services IDs in Sylius 2.0, while the current service IDs will be converted into aliases:
    
    | Current ID                                                                                                               | New Alias                                                                                |
    |--------------------------------------------------------------------------------------------------------------------------|------------------------------------------------------------------------------------------|
    | **AddressingBundle**                                                                                                     |                                                                                          |
    | `Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface`                                                       | `sylius.checker.zone_deletion`                                                           |
    | `Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface`                                           | `sylius.checker.country_provinces_deletion`                                              |
    | **ApiBundle**                                                                                                            |                                                                                          |
    | `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface`                                          | `sylius_api.applicator.archiving_shipping_method`                                        |
    | `Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicatorInterface`                                      | `sylius_api.applicator.order_state_machine_transition`                                   |
    | `Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface`                                    | `sylius_api.applicator.payment_state_machine_transition`                                 |
    | `Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicatorInterface`                              | `sylius_api.applicator.product_review_state_machine_transition`                          |
    | `Sylius\Bundle\ApiBundle\Context\UserContextInterface`                                                                   | `sylius_api.context.user.token_based`                                                    |
    | `Sylius\Bundle\ApiBundle\Provider\PathPrefixProviderInterface`                                                           | `sylius_api.provider.path_prefix`                                                        |
    | `Sylius\Bundle\ApiBundle\Provider\AdjustmentOrderProviderInterface`                                                      | `sylius_api.provider.adjustment_order`                                                   |
    | `Sylius\Bundle\ApiBundle\Changer\PaymentMethodChangerInterface`                                                          | `sylius_api.changer.payment_method`                                                      |
    | `Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface`                                                    | `sylius_api.converter.iri_to_identifier`                                                 |
    | `Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface`                                                                  | `sylius_api.mapper.address`                                                              |
    | `Sylius\Bundle\ApiBundle\Checker\AppliedCouponEligibilityCheckerInterface`                                               | `sylius_api.checker.applied_coupon_eligibility`                                          |
    | `Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface`                                                         | `sylius_api.modifier.order_address`                                                      |
    | `Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface`                                                   | `sylius_api.assigner.order_promotion_code`                                               |
    | **CoreBundle**                                                                                                           |                                                                                          |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface`                               | `sylius.applicator.catalog_promotion`                                                    |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface`                            | `sylius.applicator.catalog_promotion.action_based_discount`                              |
    | `Sylius\Component\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface`                       | `sylius.calculator.catalog_promotion.price`                                              |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface`                | `sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility`     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface`              | `sylius.processor.catalog_promotion.all_product_variant`                                 |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface`                                   | `sylius.processor.catalog_promotion.clearer`                                             |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface`                            | `sylius.processor.catalog_promotion.state`                                               |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface`                         | `sylius.processor.catalog_promotion.product`                                             |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface`                  | `sylius.processor.catalog_promotion.product_variant`                                     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface`                          | `sylius.processor.catalog_promotion.removal`                                             |
    | `Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface`                                         | `sylius.checker.product_variant_lowest_price_display`                                    |
    | `Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface`    | `sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings` |
    | `Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface`                                                | `sylius.logger.price_history.price_change`                                               |
    | `Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface`                     | `sylius.processor.price_history.product_lowest_price_before_discount`                    |
    | `Sylius\Bundle\CoreBundle\Calculator\DelayStampCalculatorInterface`                                                      | `sylius.calculator.delay_stamp`                                                          |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface`                                 | `sylius.announcer.catalog_promotion`                                                     |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface`                          | `sylius.announcer.catalog_promotion.removal`                                             |
    | `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandDispatcher\ApplyCatalogPromotionsOnVariantsCommandDispatcherInterface` | `sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants`                  |
    | `Sylius\Component\Core\Checker\CLIContextCheckerInterface`                                                               | `sylius.checker.cli_context`                                                             |
    | `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface`                                   | `sylius.provider.product_variant_map`                                                    |
    | `Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface`                                         | `sylius.checker.promotion.product_in_promotion_rule`                                     |
    | `Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface`                                           | `sylius.checker.promotion.taxon_in_promotion_rule`                                       |
    | `Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProviderInterface`                                      | `sylius.provider.channel_based_product_translation`                                      |
    | `Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface`                                                            | `sylius.provider.customer`                                                               |
    | `Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface`                                                  | `sylius.provider.statistics`                                                             |
    | `Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface`                                     | `sylius.provider.statistics.business_activity_summary`                                   |
    | `Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface`                                             | `sylius.provider.statistics.sales`                                                       |
    | `Sylius\Component\Core\Distributor\MinimumPriceDistributorInterface`                                                     | `sylius.distributor.minimum_price`                                                       |
    | `Sylius\Component\Core\Generator\ImagePathGeneratorInterface`                                                            | `sylius.generator.image_path`                                                            |
    | `Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemoverInterface`                                 | `sylius.remover.channel_pricing_log_entries`                                             |
    | `Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface`                                                    | `sylius.remover.payment.order`                                                           |
    | `Sylius\Component\Core\Cart\Resolver\CreatedByGuestFlagResolverInterface`                                                | `sylius.resolver.cart.created_by_guest_flag`                                             |
    | `Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface`                                        | `sylius.checker.order.promotions_integrity`                                              |
    | `Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface`                                                            | `sylius.resolver.customer`                                                               |
    | `Sylius\Component\Core\Statistics\Registry\OrdersTotalsProvidersRegistryInterface`                                       | `sylius.registry.statistics.orders_totals_providers`                                     |
    | `Sylius\Component\Core\Positioner\PositionerInterface`                                                                   | `sylius.positioner`                                                                      |
    | **LocaleBundle**                                                                                                         |                                                                                          |
    | `Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface`                                                         | `sylius.checker.locale_usage`                                                            |
    | **ProductBundle**                                                                                                        |
    | `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`                                                      | `sylius.resolver.product_variant`                                                        |
    | **PromotionBundle**                                                                                                      |                                                                                          |
    | `Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface`                                      | `sylius.provider.eligible_catalog_promotions`                                            |
    | **TaxonomyBundle**                                                                                                       |                                                                                          |
    | `Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface`                                                   | `sylius.custom_repository.tree.taxon`                                                    |
    
    We recommend using the new aliases introduced in Sylius 1.14 to ensure compatibility with Sylius 2.0.

1. Aliases for the following `knp_menu.menu_builder` service tags have been introduced to standardize tag aliases and will replace the incorrect aliases in Sylius 2.0:

    | Old Alias             | New Alias             |
    |-----------------------|-----------------------|
    | **AdminBundle**       |                       |
    | `sylius.admin.main`   | `sylius_admin.main`   |
    | **ShopBundle**        |                       |
    | `sylius.shop.account` | `sylius_shop.account` |

    The old alias are now deprecated and will be removed in Sylius 2.0.

1. The definition of the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`
   in the `PromotionBundle` has been deprecated and will be removed in Sylius 2.0. This definition has been copied to the `CoreBundle`.

1. The tag `sylius.catalog_promotion.action_configuration_type` for the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`
   in the `PromotionBundle` has been removed, as it has used a parameter from the `CoreBundle`. This tag has been added to the service in the `CoreBundle`.

1. The following class definitions will be moved to `PromotionBundle` in Sylius 2.0:
    - `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType`

1. The following class will be moved to `ShopBundle` in Sylius 2.0:
    - `Sylius\Bundle\CoreBundle\Theme\ChannelBasedThemeContext`

   Starting with this version, form types will be extended using the parent form instead of through form extensions,
   like it's done in the `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScopeType` and `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType` classes.

1. Classes related to legacy validation of CatalogPromotions' configuration have been deprecated and will be remove in Sylius 2.0:
    - `Sylius\Bundle\ApiBundle\Validator\CatalogPromotion\FixedDiscountActionValidator`
    - `Sylius\Bundle\ApiBundle\Validator\CatalogPromotion\ForProductsScopeValidator`
    - `Sylius\Bundle\ApiBundle\Validator\CatalogPromotion\ForTaxonsScopeValidator`
    - `Sylius\Bundle\ApiBundle\Validator\CatalogPromotion\ForVariantsScopeValidator`
    - `Sylius\Bundle\ApiBundle\Validator\CatalogPromotion\PercentageDiscountActionValidator`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionAction\FixedDiscountActionValidator`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope\ForProductsScopeValidator`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope\ForTaxonsScopeValidator`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionScope\ForVariantsScopeValidator`
    - `Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction` 
    - `Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScope` 
   Use the regular Symfony validation constraints instead.

1. The class `Sylius\Bundle\CoreBundle\Twig\StateMachineExtension` has been deprecated and will be removed in Sylius 2.0. Use `Sylius\Abstraction\StateMachine\Twig\StateMachineExtension` instead.

1. The class `Sylius\Bundle\CoreBundle\Console\Command\ShowAvailablePluginsCommand` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\CoreBundle\Console\Command\Model\PluginInfo` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddUserFormSubscriber` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\ApiBundle\Filter\Doctrine\PromotionCouponPromotionFilter` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteSubscriber` has been deprecated and will be removed in Sylius 2.0.
   It will be replaced with the `ResourceDeleteListener`.

1. Extending `\InvalidArgumentException` by `Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHandException` 
   and `Sylius\Component\Core\Inventory\Exception\NotEnoughUnitsOnHoldException` is deprecated, instead they will extend 
   `\RuntimeException` in Sylius 2.0.

1. Statistics related deprecations:
    - The class `Sylius\Bundle\AdminBundle\Provider\StatisticsDataProvider` and interface `Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface` have been deprecated and will be removed in Sylius 2.0. 
      Use `Sylius\Component\Core\Statistics\Provider\StatisticsProvider` and `Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface` instead.
    - The class `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController` has been deprecated and will be removed in Sylius 2.0.
    - The route `sylius_admin_dashboard_statistics` has been deprecated and will be removed in Sylius 2.0.
    - The class `Sylius\Component\Core\Dashboard\DashboardStatistics` has been deprecated and will be removed in Sylius 2.0.
    - The class `Sylius\Component\Core\Dashboard\DashboardStatisticsProvider` and interface `Sylius\Component\Core\Dashboard\DashboardStatisticsProviderInterface` have been deprecated and will be removed in Sylius 2.0.
    - The class `Sylius\Component\Core\Dashboard\Interval` has been deprecated and will be removed in Sylius 2.0.
    - The class `Sylius\Component\Core\Dashboard\SalesDataProvider` and interface `Sylius\Component\Core\Dashboard\SalesDataProviderInterface` have been deprecated and will be removed in Sylius 2.0.
    - The class `Sylius\Component\Core\Dashboard\SalesSummary` and interface `Sylius\Component\Core\Dashboard\SalesSummaryInterface` have been deprecated and will be removed in Sylius 2.0.

1. The following security related classes and interfaces have been deprecated, and they will be removed in 2.0:
    - `Sylius\Bundle\UserBundle\Security\UserLogin`
    - `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
    - `Sylius\Bundle\UserBundle\Security\UserPasswordHasher`
    - `Sylius\Bundle\UserBundle\Security\UserPasswordHasherInterface`

1. The following security related services and aliases have been deprecated, and they will be removed in 2.0:
    - `sylius.security.password_hasher`
    - `sylius.security.user_login`
    - `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
    - `Sylius\Component\User\Security\UserPasswordHasherInterface`

1. The constructor signature of `Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction` has been changed:
    ```diff
    use Symfony\Component\Routing\RouterInterface;

        public function __construct(
            private OrderRepositoryInterface $orderRepository,
            private OrderEmailManagerInterface|ResendOrderConfirmationEmailDispatcherInterface $orderEmailManager,
            private CsrfTokenManagerInterface $csrfTokenManager,
            private RequestStack|SessionInterface $requestStackOrSession,
    +       private ?RouterInterface $router = null,
        )
    ```

1. The following services have been deprecated and will be removed in Sylius 2.0:
    - **AdminBundle**
    - `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController`
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`
    - `Sylius\Bundle\AdminBundle\Menu\CustomerShowMenuBuilder`
    - `Sylius\Bundle\AdminBundle\Menu\OrderShowMenuBuilder`
    - `Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder`
    - `Sylius\Bundle\AdminBundle\Menu\ProductUpdateMenuBuilder`
    - `Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder`
    - `Sylius\Bundle\AdminBundle\Menu\PromotionUpdateMenuBuilder`
    - **ApiBundle**
    - `Sylius\Bundle\ApiBundle\ApiPlatform\ApiResourceConfigurationMerger`
    - `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\CachedRouteNameResolver`
    - `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\RouteNameResolver`
    - `Sylius\Bundle\ApiBundle\ApiPlatform\Factory\MergingExtractorResourceMetadataFactory`
    - `Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\LegacyResourceMetadataMerger`
    - `Sylius\Bundle\ApiBundle\ApiPlatform\ResourceMetadataPropertyValueResolver`
    - `Sylius\Bundle\ApiBundle\Controller\GetAddressLogEntryCollectionAction`
    - `Sylius\Bundle\ApiBundle\Controller\GetOrderAdjustmentsAction`
    - `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
    - `Sylius\Bundle\ApiBundle\Controller\UploadProductImageAction`
    - `Sylius\Bundle\ApiBundle\Controller\UploadTaxonImageAction`
    - `Sylius\Bundle\ApiBundle\DataPersister\AddressDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\AdminUserDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ChannelDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\CountryDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\CustomerDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\LocaleDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\MessengerDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\PaymentMethodDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ProductAttributeDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ProductDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ProductTaxonDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ProductVariantDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\PromotionCouponDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\PromotionDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ShippingMethodDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\TranslatableDataPersister`
    - `Sylius\Bundle\ApiBundle\DataPersister\ZoneDataPersister`
    - `Sylius\Bundle\ApiBundle\DataProvider\AccountResetPasswordItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\AdminOrderItemAdjustmentsSubresourceDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\AdminResetPasswordItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ChannelAwareItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ChannelsCollectionDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\CustomerItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\OrderAdjustmentsSubresourceDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\OrderItemAdjustmentsSubresourceDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\OrderItemItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\OrderItemUnitItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\PaymentItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\PaymentMethodsCollectionDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ProductAttributesSubresourceDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ProductItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ShipmentItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\ShippingMethodsCollectionDataProvider`
    - `Sylius\Bundle\ApiBundle\DataProvider\VerifyCustomerAccountItemDataProvider`
    - `Sylius\Bundle\ApiBundle\DataTransformer\ChannelCodeAwareInputCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface`
    - `Sylius\Bundle\ApiBundle\DataTransformer\LocaleCodeAwareInputCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailAwareCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailIfNotSetAwareCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserIdAwareCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\OrderTokenValueAwareInputCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\ShipmentIdAwareInputCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\DataTransformer\SubresourceIdAwareCommandDataTransformer`
    - `Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener`
    - `Sylius\Bundle\ApiBundle\OpenApi\Documentation\PathHiderDocumentationModifier`
    - `Sylius\Bundle\ApiBundle\QueryHandler\GetAddressLogEntryCollectionHandler`
    - `Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer`
    - `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ReadOperationContextBuilder`
    - `Sylius\Bundle\ApiBundle\Validator\Constraints\AccountVerificationTokenEligibilityValidator`
    - `Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidator`
    - `Sylius\Bundle\ApiBundle\Validator\ResourceInputDataPropertiesValidatorInterface`
    - `api_platform.action.post_item`
    - **CoreBundle**
    - `Sylius\Bundle\CoreBundle\Form\Extension\CountryTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\CustomerTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\LocaleTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Type\Grid\Filter\EntitiesFilterType`
    - `Sylius\Component\Core\Grid\Filter\EntitiesFilter`
    - **PayumBundle**
    - `Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout\ConvertPaymentAction`
    - `Sylius\Bundle\PayumBundle\Controller\PayumController`
    - `Sylius\Bundle\PayumBundle\Form\Type\PaypalGatewayConfigurationType`
    - `Sylius\Bundle\PayumBundle\Form\Type\StripeGatewayConfigurationType`
    - `Sylius\Bundle\PayumBundle\Validator\Constraints\GatewayFactoryExists`
    - `Sylius\Bundle\PayumBundle\Validator\GatewayFactoryExistsValidator`
    - `Sylius\Bundle\PayumBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator`
    - `sylius.form_registry.payum_gateway_config`
    - **ShopBundle**
    - `Sylius\Bundle\ShopBundle\Controller\HomepageController`
    - `Sylius\Bundle\ShopBundle\Controller\SecurityWidgetController`
    - **UiBundle**
    - `Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand`
    - `Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface`
    - `Sylius\Bundle\UiBundle\ContextProvider\DefaultContextProvider`
    - `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockDataCollector`
    - `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockRenderingHistory`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlock`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistry`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface`
    - `Sylius\Bundle\UiBundle\Renderer\DelegatingTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface`
    - `Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface`
    - `Sylius\Bundle\UiBundle\Renderer\TwigTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\Storage\FilterStorage`
    - `Sylius\Bundle\UiBundle\Storage\FilterStorageInterface`
    - `Sylius\Bundle\UiBundle\Twig\LegacySonataBlockExtension`
    - `Sylius\Bundle\UiBundle\Twig\SortByExtension`
    - `Sylius\Bundle\UiBundle\Twig\TemplateEventExtension`
    - `Sylius\Bundle\UiBundle\Twig\TestFormAttributeExtension`
    - `Sylius\Bundle\UiBundle\Twig\TestHtmlAttributeExtension`

1. The following templating helpers and its interfaces have been deprecated and will be removed in Sylius 2.0:
    - `Sylius\Bundle\CoreBundle\Templating\Helper\CheckoutStepsHelper`
    - `Sylius\Bundle\CoreBundle\Templating\Helper\PriceHelper`
    - `Sylius\Bundle\CoreBundle\Templating\Helper\VariantResolverHelper`
    - `Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper`
    - `Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface`
    - `Sylius\Bundle\InventoryBundle\Templating\Helper\InventoryHelper`
    - `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper`
    - `Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface`
    - `Sylius\Bundle\OrderBundle\Templating\Helper\AdjustmentsHelper`

1. The following constructor signatures have been changed:

   `Sylius\Bundle\CoreBundle\Twig\CheckoutStepsExtension`
    ```diff
    
    use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
    use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;

        public function __construct(
    -       private CheckoutStepsHelper $checkoutStepsHelper,
    +       private readonly CheckoutStepsHelper|OrderPaymentMethodSelectionRequirementCheckerInterface $checkoutStepsHelper,
    +       private readonly ?OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker = null,
        )
    ```

   `Sylius\Bundle\CoreBundle\Twig\PriceExtension`
    ```diff
    
    use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;

        public function __construct(
    -       private PriceHelper $helper,
    +       private readonly PriceHelper|ProductVariantPricesCalculatorInterface $helper,
        )
    ```

   `Sylius\Bundle\CoreBundle\Twig\VariantResolverExtension`
    ```diff
    
    use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

        public function __construct(
    -       private VariantResolverHelper $helper,
    +       private readonly VariantResolverHelper|ProductVariantResolverInterface $helper,
        )
    ```

    `Sylius\Bundle\CurrencyBundle\Twig\CurrencyExtension`
    ```diff

        public function __construct(
    -       private CurrencyHelperInterface $helper,
    +       private ?CurrencyHelperInterface $helper = null,
        )
    ```

   `Sylius\Bundle\InventoryBundle\Twig\InventoryExtension`
    ```diff
    use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;

        public function __construct(
    -       private InventoryHelper $helper,
    +       private InventoryHelper|AvailabilityCheckerInterface $helper
        )
    ```

   `Sylius\Bundle\LocaleBundle\Twig\LocaleExtension`
    ```diff
    use Sylius\Component\Locale\Context\LocaleContextInterface;
    use Sylius\Component\Locale\Converter\LocaleConverterInterface;

        public function __construct(
    -       private LocaleHelperInterface $localeHelper,
    +       private LocaleHelperInterface|LocaleConverterInterface $localeHelper,
    +       private ?LocaleContextInterface $localeContext = null,
        )
    ```

   `Sylius\Bundle\MoneyBundle\Twig\ConvertMoneyExtension`
    ```diff
    use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

        public function __construct(
    -       private ConvertMoneyHelperInterface $helper,
    +       private ConvertMoneyHelperInterface|CurrencyConverterInterface $helper,
        )
    ```

   `Sylius\Bundle\MoneyBundle\Twig\FormatMoneyExtension`
    ```diff
    use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

        public function __construct(
    -       private FormatMoneyHelperInterface $helper,
    +       private private FormatMoneyHelperInterface|MoneyFormatterInterface $helper,
        )
    ```

   `Sylius\Bundle\OrderBundle\Twig\AggregateAdjustmentsExtension`
    ```diff
    use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;

        public function __construct(
    -       private AdjustmentsHelper $adjustmentsHelper,
    +       private AdjustmentsHelper|AdjustmentsAggregatorInterface $adjustmentsHelper,
        )
    ```

1. The following routes has been deprecated and will be removed in Sylius 2.0:
    - `sylius_admin_ajax_all_product_variants_by_codes`
    - `sylius_admin_ajax_all_product_variants_by_phrase`
    - `sylius_admin_ajax_customer_group_by_code`
    - `sylius_admin_ajax_customer_groups_by_phrase`
    - `sylius_admin_ajax_find_product_options`
    - `sylius_admin_ajax_generate_product_slug`
    - `sylius_admin_ajax_generate_taxon_slug`
    - `sylius_admin_ajax_product_by_code`
    - `sylius_admin_ajax_product_by_name_phrase`
    - `sylius_admin_ajax_product_index`
    - `sylius_admin_ajax_product_options_by_phrase`
    - `sylius_admin_ajax_products_by_phrase`
    - `sylius_admin_ajax_product_variants_by_codes`
    - `sylius_admin_ajax_product_variants_by_phrase`
    - `sylius_admin_ajax_taxon_by_code`
    - `sylius_admin_ajax_taxon_by_name_phrase`
    - `sylius_admin_ajax_taxon_leafs`
    - `sylius_admin_ajax_taxon_root_nodes`
    - `sylius_admin_dashboard_statistics`
    - `sylius_admin_get_attribute_types`
    - `sylius_admin_get_payment_gateways`
    - `sylius_admin_get_product_attributes`
    - `sylius_admin_partial_address_log_entry_index`
    - `sylius_admin_partial_catalog_promotion_show`
    - `sylius_admin_partial_channel_index`
    - `sylius_admin_partial_customer_latest`
    - `sylius_admin_partial_customer_show`
    - `sylius_admin_partial_order_latest`
    - `sylius_admin_partial_order_latest_in_channel`
    - `sylius_admin_partial_product_show`
    - `sylius_admin_partial_promotion_show`
    - `sylius_admin_partial_taxon_show`
    - `sylius_admin_partial_taxon_tree`
    - `sylius_admin_render_attribute_forms`
    - `sylius_shop_ajax_cart_add_item`
    - `sylius_shop_ajax_cart_item_remove`
    - `sylius_shop_ajax_user_check_action`
    - `sylius_shop_partial_cart_summary`
    - `sylius_shop_partial_cart_add_item`
    - `sylius_shop_partial_channel_menu_taxon_index`
    - `sylius_shop_partial_product_association_show`
    - `sylius_shop_partial_product_index_latest`
    - `sylius_shop_partial_product_review_latest`
    - `sylius_shop_partial_product_show_by_slug`
    - `sylius_shop_partial_taxon_index_by_code`
    - `sylius_shop_partial_taxon_show_by_slug`

1. The `sylius_core.state_machine` configuration parameter is deprecated and will be removed in 2.0. 
   Use `sylius_state_machine_abstraction.state_machine` instead.

1. The `sylius_core.autoconfigure_with_attributes` and `sylius_order.autoconfigure_with_attributes` configuration parameters 
   are deprecated and will be removed in 2.0. To autoconfigure order processors and cart contexts, use corresponding attributes
   instead of interfaces:

   - `Sylius\Bundle\OrderBundle\Attribute\AsCartContext`
   - `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor`

1. The `sylius_user.resources.{name}.user.resetting.pin` configuration parameter is deprecated and will be removed in 2.0. 
   The corresponding email `reset_password_pin` and `Sylius\Bundle\UserBundle\Controller\UserController::requestPasswordResetPinAction` 
   method have been also deprecated and will be removed in 2.0. The related class `Sylius\Component\User\Security\Generator\UniquePinGenerator`
   and services have been deprecated as well:

    * `sylius.{user_type}_user.pin_generator.password_reset`
    * `sylius.{user_type}_user.pin_uniqueness_checker.password_reset`
