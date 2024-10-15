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
    | `sylius.controller.admin.dashboard.statistics`                                                             | `sylius_admin.controller.dashboard.statistics`                                       |
    | `sylius.controller.customer_statistics`                                                                    | `sylius_admin.controller.customer_statistics`                                        |
    | `sylius.controller.admin.notification`                                                                     | `sylius_admin.controller.notification`                                               |
    | `Sylius\Bundle\AdminBundle\Controller\RemoveCatalogPromotionAction`                                        | `sylius_admin.controller.remove_catalog_promotion`                                   |
    | `Sylius\Bundle\AdminBundle\Controller\RedirectHandler`                                                     | `sylius_admin.resource_controller.redirect_handler`                                  |
    | `sylius.mailer.shipment_email_manager.admin`                                                               | `sylius_admin.mailer.shipment_email_manager`                                         |
    | `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`                             | `sylius_admin.form.extension.type.catalog_promotion_action`                          |
    | `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`                              | `sylius_admin.form.extension.type.catalog_promotion_scope`                           |
    | `Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType`                                                  | `sylius_admin.form.type.request_password_reset`                                      |
    | `Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType`                                                    | `sylius_admin.form.type.reset_password`                                              |
    | `sylius.listener.shipment_ship`                                                                            | `sylius_admin.listener.shipment_ship`                                                |
    | `sylius.listener.locale`                                                                                   | `sylius_admin.listener.locale`                                                       |
    | `sylius.event_subscriber.admin_cache_control_subscriber`                                                   | `sylius_admin.event_subscriber.admin_section_cache_control`                          |
    | `sylius.event_subscriber.admin_filter_subscriber`                                                          | `sylius_admin.event_subscriber.admin_filter`                                         |
    | `sylius.admin.menu_builder.customer.show`                                                                  | `sylius_admin.menu_builder.customer.show`                                            |
    | `sylius.admin.menu_builder.main`                                                                           | `sylius_admin.menu_builder.main`                                                     |
    | `sylius.admin.menu_builder.order.show`                                                                     | `sylius_admin.menu_builder.order.show`                                               |
    | `sylius.admin.menu_builder.product_form`                                                                   | `sylius_admin.menu_builder.product.form`                                             |
    | `sylius.admin.menu_builder.product.update`                                                                 | `sylius_admin.menu_builder.product.update`                                           |
    | `sylius.admin.menu_builder.product_variant_form`                                                           | `sylius_admin.menu_builder.product_variant.form`                                     |
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
    | **OrderBundle**                                                                                            |
    | `sylius.order_modifier`                                                                                    | `sylius.modifier.order`                                                              |
    | `sylius.order_item_quantity_modifier`                                                                      | `sylius.modifier.order_item_quantity`                                                |
    | `sylius.order_number_assigner`                                                                             | `sylius.number_assigner.order_number`                                                |
    | `sylius.adjustments_aggregator`                                                                            | `sylius.aggregator.adjustments_by_label`                                             |
    | `sylius.expired_carts_remover`                                                                             | `sylius.remover.expired_carts`                                                       |
    | `sylius.sequential_order_number_generator`                                                                 | `sylius.number_generator.sequential_order`                                           |
    | `Sylius\Bundle\OrderBundle\Console\Command\RemoveExpiredCartsCommand`                                      | `sylius.console.command.remove_expired_carts`                                        |
    | **ProductBundle**                                                                                          |
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
    | `sylius.controller.shop.homepage`                                                                          | `sylius_shop.controller.homepage`                                                    |
    | `sylius.controller.shop.currency_switch`                                                                   | `sylius_shop.controller.currency_switch`                                             |
    | `sylius.controller.shop.locale_switch`                                                                     | `sylius_shop.controller.locale_switch`                                               |
    | `sylius.controller.shop.security_widget`                                                                   | `sylius_shop.controller.security_widget`                                             |
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
    | `Sylius\Bundle\UiBundle\Storage\FilterStorage`                                                             | `sylius.twig.storage.filter`                                                         |
    | **UserBundle**                                                                                             |                                                                                      |
    | `Sylius\Bundle\UserBundle\Console\Command\DemoteUserCommand`                                               | `sylius.console.command.demote_user`                                                 |
    | `Sylius\Bundle\UserBundle\Console\Command\PromoteUserCommand`                                              | `sylius.console.command.promote_user`                                                |

   The old service IDs are now deprecated and will be removed in Sylius 2.0. Please update your service references accordingly to ensure compatibility with Sylius 2.0.

1. For the following services, new aliases have been added in Sylius 1.14.
   These aliases will become the primary services IDs in Sylius 2.0, while the current service IDs will be converted into aliases:
    
    | Current ID                                                                          | New Alias                                     |
    |-------------------------------------------------------------------------------------|-----------------------------------------------|
    | **AddressingBundle**                                                                |                                               |
    | `Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface`                  | `sylius.checker.zone_deletion`                |
    | `Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface`      | `sylius.checker.country_provinces_deletion`   |
    | **LocaleBundle**                                                                    |                                               |
    | `Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface`                    | `sylius.checker.locale_usage`                 |
    | **ProductBundle**                                                                   |
    | `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`                 | `sylius.resolver.product_variant`             |
    | **PromotionBundle**                                                                 |                                               |
    | `Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface` | `sylius.provider.eligible_catalog_promotions` |
    | **TaxonomyBundle**                                                                  |                                               |
    | `Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface`              | `sylius.custom_repository.tree.taxon`         |
    
    We recommend using the new aliases introduced in Sylius 1.14 to ensure compatibility with Sylius 2.0.

1. Aliases for the following `knp_menu.menu_builder` service tags have been introduced to standardize tag aliases and will replace the incorrect aliases in Sylius 2.0:

    | Old Alias                           | New Alias                           |
    |-------------------------------------|-------------------------------------|
    | **AdminBundle**                     |                                     |
    | `sylius.admin.customer.show`        | `sylius_admin.customer.show`        |
    | `sylius.admin.main`                 | `sylius_admin.main`                 |
    | `sylius.admin.order.show`           | `sylius_admin.order.show`           |
    | `sylius.admin.product_form`         | `sylius_admin.product.form`         |
    | `sylius.admin.product.update`       | `sylius_admin.product.update`       |
    | `sylius.admin.product_variant_form` | `sylius_admin.product_variant.form` |
    | **ShopBundle**                      |                                     |
    | `sylius.shop.account`               | `sylius_shop.account`               |

    The old alias are now deprecated and will be removed in Sylius 2.0.

1. The definition of the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`
   in the `PromotionBundle` has been deprecated and will be removed in Sylius 2.0. This definition has been copied to the `CoreBundle`.

1. The tag `sylius.catalog_promotion.action_configuration_type` for the service `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`
   in the `PromotionBundle` has been removed, as it has used a parameter from the `CoreBundle`. This tag has been added to the service in the `CoreBundle`.

1. The following class definitions will be moved to `PromotionBundle` in Sylius 2.0:
    - `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType`

1. The following form extensions have been deprecated and will be removed in Sylius 2.0:
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\CustomerTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\LocaleTypeExtension`

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
    - `Sylius\Bundle\AdminBundle\Menu\PromotionUpdateMenuBuilder`
    - `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockRenderingHistory`
    - `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockDataCollector`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateBlockRendererInterface`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateEventRendererInterface`
    - `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\ContextProvider\DefaultContextProvider`
    - `Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlock`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistry`
    - `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface`
    - `Sylius\Bundle\UiBundle\Renderer\TwigTemplateBlockRenderer`
    - `Sylius\Bundle\UiBundle\Renderer\DelegatingTemplateEventRenderer`
    - `Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand`
    - `Sylius\Bundle\UiBundle\Twig\TemplateEventExtension`
    - `Sylius\Bundle\UiBundle\Twig\LegacySonataBlockExtension`

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
