# UPGRADE FROM `v1.12.X` TO `v1.13.0`

1. There has been a naw parameter added to specify the validation groups for given gateway factory.
   If you have any custom validation groups for your factory, you need to add them to your `config/packages/_sylius.yaml` file.
   Also, if you have your own gateway factory and want to add your validation groups you can add another entry to the `validation_groups` configuration node.
   It is handled by `GatewayConfigGroupsGenerator` and it resolves the groups based on the passed factory name.
    ```yaml
    sylius_payum:
        gateway_config:
            validation_groups:
                paypal_express_checkout:
                    - 'sylius'
                    - 'sylius_paypal_express_checkout'
                stripe_checkout:
                    - 'sylius'
                    - 'sylius_stripe_checkout'
                your_gateway:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```

1. There have been a naw parameters added to specify the validation groups for given shipping method rule and calculator.
   If you have any custom validation groups for your calculator or rules, you need to add them to your `config/packages/_sylius.yaml` file.
   Also, if you have your own shipping method rule or calculator and want to add your validation groups you can add another key to the `validation_groups` parameter.

    ```yaml
    sylius_shipping:
        shipping_method_rule:
            validation_groups:
                total_weight_greater_than_or_equal:
                    - 'sylius'
                    - 'sylius_shipping_method_rule_total_weight'
                order_total_greater_than_or_equal:
                    - 'sylius'
                    - 'sylius_shipping_method_rule_order_total'
                your_shipping_method_rule:
                    - 'sylius'
                    - 'your_custom_validation_group'
        shipping_method_calculator:
            validation_groups:
                flat_rate:
                    - 'sylius'
                    - 'sylius_shipping_method_calculator_rate'
                per_unit_rate:
                    - 'sylius'
                    - 'sylius_shipping_method_calculator_rate'
                your_shipping_method_calculator:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```

1. Class `Sylius\Component\Core\Promotion\Updater\Rule\TotalOfItemsFromTaxonRuleUpdater` has been deprecated, as it is no more used.

1. Class `Sylius\Component\Core\Promotion\Updater\Rule\ContainsProductRuleUpdater` has been deprecated, as it is no more used.

1. Class `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionChoiceType` has been deprecated.
   Use `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionAutocompleteType` instead.

1. Using `parentId` query parameter to generate slug in `Sylius\Bundle\TaxonomyBundle\Controller\TaxonSlugController` has been deprecated.
   Use the `parentCode` query parameter instead.

1. Starting with Sylius 1.13, the `SyliusPriceHistoryPlugin` is included.
   If you are currently using the plugin in your project, we recommend following the upgrade guide located [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13.md).

1. The `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion` command and its handler
   `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler` have been deprecated.
   Use `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion` command instead.

1. Passing `Symfony\Component\Messenger\MessageBusInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second and third argument is deprecated.

1. Not passing `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface` to `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
   as a second argument is deprecated.

1. Not passing `Doctrine\Persistence\ObjectManager` to `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
   as a fifth argument is deprecated.

1. To ease customization we've introduces attributes for some services in `1.13`:
   - `Sylius\Bundle\OrderBundle\Attribute\AsCartContext` for cart contexts
   - `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor` for order processors
   - `Sylius\Bundle\ProductBundle\Attribute\AsProductVariantResolver` for product variant resolvers

   By default, Sylius still configures them using interfaces, but this way you cannot define a priority.
   If you want to define a priority, you need to set the following configuration in your `_sylius.yaml` file:
   ```yaml
   sylius_core:
       autoconfigure_with_attributes: true
   ```
   and use one of the new attributes accordingly to the type of your class, e.g.:
   ```php
    <?php

    declare(strict_types=1);

    namespace App\OrderProcessor;

    use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
    use Sylius\Component\Order\Model\OrderInterface;
    use Sylius\Component\Order\Processor\OrderProcessorInterface;

    #[AsOrderProcessor(/*priority: 10*/)] //priority is optional
    final class OrderProcessorWithAttributeStub implements OrderProcessorInterface
    {
        public function process(OrderInterface $order): void
        {
        }
    }
   ```

1. The constructors of `Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand` and `Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand` have been changed:

    ```diff
      public function __construct(
          FactoryInterface $adjustmentFactory,
    -     private FilterInterface $priceRangeFilter,
    -     private FilterInterface $taxonFilter,
    -     private FilterInterface $productFilter,
    +     private ?FilterInterface $priceRangeFilter,
    +     private ?FilterInterface $taxonFilter,
    +     private ?FilterInterface $productFilter,
    +     private ?FilterInterface $compositeFilter = null,
      ) {
          ...
      }
    ```
   Passing several arguments of `Sylius\Component\Core\Promotion\Filter\FilterInterface` on second, third and fourth position has been deprecated.

1. Not passing `Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface` 
   to `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
   as a first argument is deprecated.

1. Not passing an instance of `Symfony\Component\PropertyAccess\PropertyAccessorInterface`
   to `Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator`
   as the second argument is deprecated.

1. Not passing an instance of `Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface`
   and a collection of unprocessable order states to `Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor`
   as the third and fourth arguments respectively is deprecated.

1. Not passing an instance of `Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface`
   to `Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator` and to `Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator`
   as the last argument is deprecated.

1. Class `\Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculator` has been deprecated. Order items subtotal calculation
   is now available on the Order model `\Sylius\Component\Core\Model\Order::getItemsSubtotal`.

1. The way of getting variants prices based on options has been changed,
   as such the following services were deprecated, please use their new counterpart.
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface`
   * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProvider` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
   * instead of `Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper` use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
   * instead of `Sylius\Bundle\CoreBundle\Twig\ProductVariantsPricesExtension` use `Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension`

   Subsequently, the `sylius_product_variant_prices` twig function is deprecated, use `sylius_product_variants_map` instead.

   To add more data per variant create a service implementing the `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface` and tag it with `sylius.product_variant_data_map_provider`.

1. Using Guzzle 6 has been deprecated in favor of Symfony HTTP Client. If you want to still use Guzzle 6 or Guzzle 7,
   you need to install `composer require php-http/guzzle6-adapter` or `composer require php-http/guzzle7-adapter`
   depending on your Guzzle version.
   Subsequently, you need to register the adapter as a `Psr\Http\Client\ClientInterface` service as the following:
    ```yaml
        services:
            Psr\Http\Client\ClientInterface:
                class: Http\Adapter\Guzzle7\Client # for Guzzle 6 use Http\Adapter\Guzzle6\Client instead
    ```

1. The constructor of `Sylius\Bundle\AdminBundle\Controller\NotificationController` has been changed:

    ```diff
        public function __construct(
    -       private ClientInterface $client,
    -       private MessageFactory $messageFactory,
    +       private ClientInterface|DeprecatedClientInterface $client,
    +       private RequestFactoryInterface|MessageFactory $requestFactory,
            private string $hubUri,
            private string $environment,
    +       private ?StreamFactoryInterface $streamFactory = null,
        ) {
            ...
        }
    ```

1. The `sylius.http_message_factory` service has been deprecated. Use `Psr\Http\Message\RequestFactoryInterface` instead.

1. The `sylius.http_client` has become an alias to `psr18.http_client` service.

1. The `sylius.payum.http_client` has become a service ID of newly created `Sylius\Bundle\PayumBundle\HttpClient\HttpClient`.

1. Validation translation key `sylius.review.rating.range` has been replaced by `sylius.review.rating.not_in_range` in all places used by Sylius. The `sylius.review.rating.range` has been left for backward compatibility and will be removed in Sylius 2.0.

1. The `payum/payum` package has been replaced by concrete packages like `payum/core`, `payum/offline` or `payum/paypal-express-checkout-nvp`. If you need any other component so far provided by `payum/payum` package, you need to install it explicitly.

1. PostgreSQL migration support has been introduced. If you are using PostgreSQL, we assume that you have already created a database schema in some way.
   All you need to do is run migrations, which will mark all migrations created before Sylius 1.13 as executed.

1. Not passing an `$entityManager` and passing a `$doctrineRegistry` to `Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProvider` constructor is deprecated and will be prohibited in Sylius 2.0.

1. Product variants resolving has been refactored for better extendability.
   The tag `sylius.product_variant_resolver.default` has been removed as it was never used.

   All internal usages of service `sylius.product_variant_resolver.default` have been switched to `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`, if you have been using the
   `sylius.product_variant_resolver.default` service apply this change accordingly.

1. Due to optimizations of the Order's grid the `Sylius\Component\Core\Repository\OrderRepositoryInterface::createSearchListQueryBuilder` method bas been deprecated in both the interface and the class, and replaced by `Sylius\Component\Core\Repository\OrderRepositoryInterface::createCriteriaAwareSearchListQueryBuilder`.
   Also `Sylius\Component\Core\Repository\OrderRepositoryInterface::createByCustomerIdQueryBuilder` has been deprecated in both the interface and the class, and replaced by `Sylius\Component\Core\Repository\OrderRepositoryInterface::createByCustomerIdCriteriaAwareQueryBuilder` for the same reason. Both changes affect
   `sylius_admin_order` and `sylius_admin_customer_order` grids configuration.

1. We have explicitly added relationships between product and reviews and between product and attributes in XML mappings.
   Because of that, the subscribers `Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber` 
   and `Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber` have changed so that it does not add 
   a relationship if one already exists. If you have overwritten or decorated it, there may be a need to update it. 

1. Passing an instance of `Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface` as the first argument
    to `Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType` has been deprecated.

1. The `sylius_admin_ajax_taxon_move` route has been deprecated. If you're relaying on it, consider migrating to new
    `sylius_admin_ajax_taxon_move_up` and `sylius_admin_ajax_taxon_move_down` routes.

1. Not passing a `$fileLocator` to `Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory` constructor is deprecated and will be prohibited in Sylius 2.0.

1. Interface `Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface` and class `Sylius\Bundle\ShopBundle\Twig\OrderItemsSubtotalExtension` responsible for the `sylius_order_items_subtotal` twig function have been deprecated and will be removed in Sylius 2.0.
   Use the `::getItemsSubtotal()` method from the `Order` class instead.

1. The `Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentFixture` has been deprecated. Use `Sylius\Bundle\CoreBundle\Fixture\PaymentFixture` instead.

1. Not passing a `$router` to `Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController` as the fourth argument is deprecated and will be prohibited in Sylius 2.0.

1. The `Sylius\Bundle\CoreBundle\Provider\SessionProvider` has been deprecated and will be removed in Sylius 2.0.

1. Interface `Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface` has been deprecated and will be removed in Sylius 2.0.

1. Both `getCreatedByGuest` and `setCreatedByGuest` methods were deprecated on `\Sylius\Component\Core\Model\OrderInterface`.
    Please use `isCreatedByGuest` instead of the first one. The latter is a part of the `setCustomerWithAuthorization` logic
    and should be used only this way.

1. The `Sylius\Bundle\ShippingBundle\Provider\Calendar` and `Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider` have been deprecated and will be removed in Sylius 2.0. Use `Symfony\Component\Clock\Clock` instead. Note: this class is available since Symfony 6.2.

1. In the `sylius_payment` state machine of `PaymentBundle`, there has been a change in the state name:
    - State name change:
        - From: `void`
        - To: `unknown`

1. In the `sylius_payment` state machine of `PaymentBundle`, a new state `authorized` has been introduced, along with a new transition:
    - Transition `authorize`:
        - From states: [`new`, `processing`]
        - To state: `authorized`

   Due to that the following transitions have been updated:
    - Transition `complete`:
        - From states: [`new`, `processing`, `authorized`]
        - To state: `completed`
    - Transition `fail`:
        - From states: [`new`, `processing`, `authorized`]
        - To state: `failed`
    - Transition `cancel`:
        - From states: [`new`, `processing`, `authorized`]
        - To state: `cancelled`
    - Transition `void`:
        - From states: [`new`, `processing`, `authorized`]
        - To state: `unknown`

1. The `sylius_payment` state machine of `CoreBundle` has been updated to allow failing an authorized payment:
    ```diff
        fail:
    -       from: [new, processing]
    +       from: [new, processing, authorized]
            to: failed
    ```

1. Change in the `Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionExampleFactory` constructor:
   Added the `$localeRepository` argument to the constructor of the `PromotionExampleFactory` class. Not passing an instance of `RepositoryInterface` for the `locale` entity repository in `$localeRepository` was marked as deprecated and will be prohibited in Sylius 2.0.

1. The `Regex` constraint has been removed from `Sylius\Component\Addressing\Model\Country` in favour of the `Country` constraint.
   Due to that, it's translation message `sylius.country.code.regex` was also removed.

1. The `redirectToCartSummary` protected method of `Sylius\Bundle\OrderBundle\Controller\OrderController` has been deprecated as it was never used and will be removed in Sylius 2.0.

1. Interface `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface` has been refactored and is now deprecated. It now extends a new interface `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionReadInterface`, which contains only getter methods.
    - If your services or custom implementations previously relied on `PromotionCouponGeneratorInstructionInterface` for read operations, you should now use `PromotionCouponGeneratorInstructionReadInterface` for better clarity and separation of concerns.
    - This change is backward compatible as long as your implementations or services were using only the getter methods from `PromotionCouponGeneratorInstructionInterface`. However, if you also utilized setter methods, you should continue using `PromotionCouponGeneratorInstructionInterface`.

1. A new parameter has been added to specify the validation groups for a given promotion action. 
   If you have any custom validation groups for your promotion action, you need to add them to your `config/packages/_sylius.yaml` file. 
   Additionally, if you have your own promotion action and want to add your validation groups, you can add another key to the `promotion_action.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\PromotionActionGroupValidator` and it resolves the groups based on the type of the passed promotion action.

    ```yaml
    sylius_promotion:
        promotion_action:
            validation_groups:
                order_percentage_discount:
                    - 'sylius'
                    - 'sylius_promotion_action_order_percentage_discount'
                shipping_percentage_discount:
                    - 'sylius'
                    - 'sylius_promotion_action_shipping_percentage_discount'
                your_promotion_action:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```
   Along with this update, constraints have been removed from specific action form types. The affected form types include:
   - `Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType`
   - `Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType`
   - `Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitFixedDiscountConfigurationType`
   - `Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitPercentageDiscountConfigurationType`
   
   The constraints previously defined in these forms are now in `src/Sylius/Bundle/CoreBundle/Resources/config/validation/PromotionAction.xml` and managed via the new validation groups parameters in the configuration.

1. A new parameter has been added to specify the validation groups for a given promotion rule.
   If you have any custom validation groups for your promotion rule, you need to add them to your `config/packages/_sylius.yaml` file.
   Additionally, if you have your own promotion rule and want to add your validation groups, you can add another key to the `promotion_rule.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator` and it resolves the groups based on the type of the passed promotion rule.

    ```yaml
    sylius_promotion:
        promotion_rule:
            validation_groups:
                cart_quantity:
                    - 'sylius'
                    - 'sylius_promotion_rule_cart_quantity'
                customer_group:
                    - 'sylius'
                    - 'sylius_promotion_rule_customer_group'
                your_promotion_rule:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```
   Along with this update, constraints have been removed from specific rule form types. The affected form types include:
    - `Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType`
    - `Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\NthOrderConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Rule\CartQuantityConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Rule\ItemTotalConfigurationType`

   The constraints previously defined in these forms are now in `src/Sylius/Bundle/CoreBundle/Resources/config/validation/PromotionRule.xml` and managed via the new validation groups parameters in the configuration.
