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
    
    | Old ID                                                                         | New ID                                                                              |
    |--------------------------------------------------------------------------------|-------------------------------------------------------------------------------------|
    | `sylius.form.type.attribute_type.select.choices_collection`                    | `sylius.form.type.attribute_type.configuration.select_attribute_choices_collection` |
    | `sylius.attribute_type.select.value.translations`                              | `sylius.form.type.attribute_type.configuration.select_attribute_value_translations` |
    | `sylius.validator.valid_text_attribute`                                        | `sylius.validator.valid_text_attribute_configuration`                               |
    | `sylius.validator.valid_select_attribute`                                      | `sylius.validator.valid_select_attribute_configuration`                             |
    | `sylius.province_naming_provider`                                              | `sylius.provider.province_naming`                                                   |
    | `sylius.zone_matcher`                                                          | `sylius.matcher.zone`                                                               |
    | `sylius.address_comparator`                                                    | `sylius.comparator.address`                                                         |
    | `sylius.channel_collector`                                                     | `sylius.collector.channel`                                                          |
    | `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext`           | `sylius.context.locale.request_header_based`                                        |
    | `sylius.locale_collection_provider`                                            | `sylius.provider.locale_collection`                                                 |
    | `sylius.locale_collection_provider.cahced`                                     | `sylius.provider.locale_collection.cached`                                          |
    | `sylius.locale_provider`                                                       | `sylius.provider.locale`                                                            |
    | `sylius.locale_converter`                                                      | `sylius.converter.locale`                                                           |
    | `Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener` | `sylius.doctrine.listener.locale_modification`                                      |
    | `sylius.twig.extension.convert_amount`                                         | `sylius.twig.extension.convert_money`                                               |
    | `sylius.twig.extension.money`                                                  | `sylius.twig.extension.format_money`                                                |
    | `sylius.money_formatter`                                                       | `sylius.formatter.money`                                                            |
    | `sylius.currency_converter`                                                    | `sylius.converter.currency`                                                         |
    | `sylius.currency_name_converter`                                               | `sylius.converter.currency_name`                                                    |
    | `sylius.availability_checker.default`                                          | `sylius.availability_checker`                                                       |

   The old service IDs are now deprecated and will be removed in Sylius 2.0. Please update your service references accordingly to ensure compatibility with Sylius 2.0.

1. For the following services, new aliases have been added in Sylius 1.14.
   These aliases will become the primary services IDs in Sylius 2.0, while the current service IDs will be converted into aliases:
    
    | Current ID                                                                     | New Alias                                   |
    |--------------------------------------------------------------------------------|---------------------------------------------|
    | `Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface`             | `sylius.checker.zone_deletion`              |
    | `Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface` | `sylius.checker.country_provinces_deletion` |
    | `Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface`               | `sylius.checker.locale_usage`               |
    
    We recommend using the new aliases introduced in Sylius 1.14 to ensure compatibility with Sylius 2.0.

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
