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

1. The following form extensions have been deprecated and will be removed in Sylius 2.0:
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`
    - `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\CustomerTypeExtension`
    - `Sylius\Bundle\CoreBundle\Form\Extension\LocaleTypeExtension`

   Starting with this version, form types will be extended using the parent form instead of through form extensions,
   like it's done in the `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScopeType` and `Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType` classes.

1. The class `Sylius\Bundle\CoreBundle\Twig\StateMachineExtension` has been deprecated and will be removed in Sylius 2.0. Use `Sylius\Abstraction\StateMachine\Twig\StateMachineExtension` instead.

1. The class `Sylius\Bundle\CoreBundle\Console\Command\ShowAvailablePluginsCommand` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\CoreBundle\Console\Command\Model\PluginInfo` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddUserFormSubscriber` has been deprecated and will be removed in Sylius 2.0.

1. The class `Sylius\Bundle\ApiBundle\Filter\Doctrine\PromotionCouponPromotionFilter` has been deprecated and will be removed in Sylius 2.0.

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
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelper`
    - `Sylius\Bundle\MoneyBundle\Templating\Helper\FormatMoneyHelperInterface`

1. The following constructor signatures have been changed:

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
