# UPGRADING FROM `v1.13.6` TO `v1.13.7`

1. The `sylius.product_review.average_rating_updater` has changed its `doctrine.event_listener` event 
   from `postRemove` to `preRemove`.

# UPGRADING FROM `v1.13.1` TO `v1.13.2`

1. Due to a bug that was causing wrong calculation of available stock during completing a payment [REF](https://github.com/Sylius/Sylius/issues/16160),
   The constructor of `Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener` has been modified as follows:

   ```diff
    public function __construct(
    +   private OrderItemAvailabilityCheckerInterface|AvailabilityCheckerInterface $availabilityChecker,
    -   private AvailabilityCheckerInterface $availabilityChecker,
    )
    ```

   If you have overwritten the service or its argument, check the correct functioning.

# UPGRADE FROM `v1.12.X` TO `v1.13.0`

## Preconditions

### PHP 8.1 support

Sylius 1.13 comes with a bump of minimum PHP version to 8.1. We strongly advice to make upgrade process step by step,
so it is highly recommended to update your PHP version while being still on Sylius 1.12. After ensuring, that the
previous step succeeds, you may move forward to the Sylius 1.13 update.

### Symfony support

The minimum supported version of Symfony 6 has been bumped up to 6.4.
Sylius 1.13 supports both long-term supported Symfony versions: 5.4 and 6.4.

### Price History Plugin

Starting with Sylius 1.13, the functionality of [SyliusPriceHistoryPlugin](https://github.com/Sylius/PriceHistoryPlugin)
is included in Core.
If you are currently using the plugin in your project, we recommend following the upgrade guide
located [here](UPGRADE-FROM-1.12-WITH-PRICE-HISTORY-PLUGIN-TO-1.13.md).

## Main update

To ease the update process, we have grouped the changes into the following categories:

### Dependencies

1. Starting with Sylius `1.13` we provided a possibility to use the Symfony Workflow as your State Machine. To allow a
   smooth transition we created a new package called `sylius/state-machine-abstraction`, which provides a configurable
   abstraction, allowing you to define which adapter should be used (Winzou State Machine or Symfony Workflow) per
   graph.

1. The use of `sylius/calendar` package is deprecated in favor of `symfony/clock` package globally.

1. The `payum/payum` package has been replaced by concrete packages like `payum/core`, `payum/offline`
   or `payum/paypal-express-checkout-nvp`. If you need any other component so far provided by `payum/payum` package, you
   need to install it explicitly.

### Constructors signature changes

1. The following AdminBundle constructor signatures have been changed:

   `Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction`
    ```diff
    use Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface;
    use Sylius\Bundle\CoreBundle\MessageDispatcher\ResendOrderConfirmationEmailDispatcherInterface;

        public function __construct(
            OrderRepositoryInterface $orderRepository,
    -       OrderEmailManagerInterface $orderEmailManager,
    +       ResendOrderConfirmationEmailDispatcherInterface $orderEmailManager,
            CsrfTokenManagerInterface $csrfTokenManager,
            RequestStack $requestStackOrSession,
        )
    ```
   Starting from Sylius 2.0, the `$orderEmailManager` argument will be renamed
   to `$resendOrderConfirmationEmailDispatcher`.

   `Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction`
    ```diff
    use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
    use Sylius\Bundle\CoreBundle\MessageDispatcher\ResendShipmentConfirmationEmailDispatcherInterface;

        public function __construct(
            ShipmentRepositoryInterface $shipmentRepository,
    -       ShipmentEmailManagerInterface $shipmentEmailManager,
    +       ResendShipmentConfirmationEmailDispatcherInterface $shipmentEmailManager,
            CsrfTokenManagerInterface $csrfTokenManager,
            RequestStack $requestStackOrSession,
        )
    ```
   Starting from Sylius 2.0, the `$shipmentEmailManager` argument will be renamed
   to `$resendShipmentConfirmationEmailDispatcher`.

   `Sylius\Bundle\AdminBundle\Controller\NotificationController`
    ```diff
    use GuzzleHttp\ClientInterface as DeprecatedClientInterface;
    use Http\Message\MessageFactory;
    use Psr\Http\Client\ClientInterface;
    use Psr\Http\Message\RequestFactoryInterface;
    use Psr\Http\Message\StreamFactoryInterface;   

        public function __construct(
    -       DeprecatedClientInterface $client,
    +       ClientInterface $client,
    -       MessageFactory $messageFactory,
    +       RequestFactoryInterface $requestFactory,
            string $hubUri,
            string $environment,
    +       StreamFactoryInterface $streamFactory,
        )
    ```

   `Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController`
    ```diff
    use Symfony\Component\Routing\RouterInterface;

        public function __construct(
            UserImpersonatorInterface $impersonator,
            AuthorizationCheckerInterface $authorizationChecker,
            UserProviderInterface $userProvider,
    +       RouterInterface $router,
            string $authorizationRole,
        )
    ```

   `Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener`
    ```diff
    use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface as DeprecatedShipmentEmailManagerInterface;
    use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;

        public function __construct(
    -       DeprecatedShipmentEmailManagerInterface $shipmentEmailManager,
    +       ShipmentEmailManagerInterface $shipmentEmailManager,
        )
    ```

1. The following AttributeBundle constructor signature has been changed:

   `Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType`
    ```diff
    use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

        public function __construct(
    -       TranslationLocaleProviderInterface $localeProvider,
        )
    ```

1. The following CoreBundle constructor signatures have been changed:

   `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
    ```diff
    use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
    use Symfony\Component\Messenger\MessageBusInterface;

        public function __construct(
            private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    +       private CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
    -       private MessageBusInterface $commandBus,
    -       private MessageBusInterface $eventBus,
        )
    ```

   `Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory`
    ```diff
    use Symfony\Component\Config\FileLocatorInterface;
   
        public function __construct(
            FactoryInterface $productFactory,
            FactoryInterface $productVariantFactory,
            FactoryInterface $channelPricingFactory,
            ProductVariantGeneratorInterface $variantGenerator,
            FactoryInterface $productAttributeValueFactory,
            FactoryInterface $productImageFactory,
            FactoryInterface $productTaxonFactory,
            ImageUploaderInterface $imageUploader,
            SlugGeneratorInterface $slugGenerator,
            RepositoryInterface $taxonRepository,
            RepositoryInterface $productAttributeRepository,
            RepositoryInterface $productOptionRepository,
            RepositoryInterface $channelRepository,
            RepositoryInterface $localeRepository,
            RepositoryInterface $taxCategoryRepository,
    +       FileLocatorInterface $fileLocator,
        )
    ```

   `Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionExampleFactory`
    ```diff
    use Sylius\Component\Resource\Repository\RepositoryInterface;
   
        public function __construct(
            FactoryInterface $promotionFactory,
            ExampleFactoryInterface $promotionRuleExampleFactory,
            ExampleFactoryInterface $promotionActionExampleFactory,
            ChannelRepositoryInterface $channelRepository,
            FactoryInterface $couponFactory,
    +       RepositoryInterface $localeRepository,
        )
    ```

   `Sylius\Bundle\CoreBundle\Installer\Provider\DatabaseSetupCommandsProvider`
    ```diff
    use Doctrine\Bundle\DoctrineBundle\Registry;
    use Doctrine\ORM\EntityManagerInterface;

        public function __construct(
    -       Registry $doctrineRegistry,
    +       EntityManagerInterface $entityManager,
        )
    ```

   `Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup`
    ```diff
    use Symfony\Component\Filesystem\Filesystem;

        public function __construct(
            RepositoryInterface $localeRepository,
            FactoryInterface $localeFactory,
            string $locale,
    +       Filesystem $filesystem,
    +       string $localeParameterFilePath,
        )
    ```

   `Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword`
     ```diff
         public function __construct( 
     -      string $resetPasswordToken,
     +      string $token,
            ?string $newPassword = null,
            ?string $confirmNewPassword = null,
         )
     ```

   `Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account\SendResetPasswordEmailHandler`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
    use Sylius\Component\Mailer\Sender\SenderInterface;

        public function __construct(
            private UserRepositoryInterface $shopUserRepository,
    -       private SenderInterface $emailSender,
    +       private ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
        )
    ```

   `Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator`
    ```diff
    use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

        public function __construct(
            ManagerRegistry $registry,
    +       PropertyAccessorInterface $accessor,
        )
    ```

1. The following ShopBundle constructor signatures have been changed:

   `Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
    use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface as DeprecatedOrderEmailManagerInterface;
    
        public function __construct(
    -       DeprecatedOrderEmailManagerInterface $orderEmailManager,
    +       OrderEmailManagerInterface $orderEmailManager,
        )
    ```

   `Sylius\Bundle\ShopBundle\Controller\ContactController`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
    use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface as DeprecatedContactEmailManagerInterface;
    
        public function __construct(
            RouterInterface $router,
            FormFactoryInterface $formFactory,
            Environment $templatingEngine,
            ChannelContextInterface $channelContext,
            CustomerContextInterface $customerContext,
            LocaleContextInterface $localeContext,
    -       DeprecatedContactEmailManagerInterface $contactEmailManager,
    +       ContactEmailManagerInterface $contactEmailManager,
        )
    ```

1. The following components' constructor signatures have been changed:

   `Sylius\Component\Addressing\Matcher\ZoneMatcher`
    ```diff
    use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;

        public function __construct(
    -       private RepositoryInterface $zoneRepository,
    +       private ZoneRepositoryInterface $zoneRepository,
        )
    ```

   `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
    ```diff
    use Doctrine\Persistence\ObjectManager;
    use SM\Factory\Factory;
    use Sylius\Abstraction\StateMachine\StateMachineInterface;

        public function __construct(
            OrderRepositoryInterface $orderRepository,
    -       Factory $stateMachineFactory,
    +       StateMachineInterface $stateMachineFactory,
            string $expirationPeriod,
            LoggerInterface $logger,
    +       ObjectManager $orderManager,
            int $batchSize = 100,
        )
    ```

   `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
    ```diff
    use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;

        public function __construct(
    +       ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        )
    ```

   `Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor`
    ```diff
    use Sylius\Component\Core\Payment\Remover\OrderPaymentsRemoverInterface;

        /** @param array<string> $unprocessableOrderStates */
        public function __construct(
            OrderPaymentProviderInterface $orderPaymentProvider,
            string $targetState = PaymentInterface::STATE_CART,
    +       OrderPaymentsRemoverInterface $orderPaymentsRemover,
    +       array $unprocessableOrderStates,
       )
    ```

   `Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator`
    ```diff
    use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

        public function __construct(
            CalculatorInterface $calculator,
            AdjustmentFactoryInterface $adjustmentFactory,
            IntegerDistributorInterface $distributor,
            TaxRateResolverInterface $taxRateResolver,
    +       ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        ) 
    ```

   `Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator`
    ```diff
    use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;

        public function __construct(
            CalculatorInterface $calculator,
            AdjustmentFactoryInterface $adjustmentFactory,
            TaxRateResolverInterface $taxRateResolver,
    +       ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
        ) 
    ```

   `Sylius\Component\Core\Taxation\Applicator\OrderItemUnitsTaxesApplicator`
    ```diff
    use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;

        public function __construct(
    -       RuleCheckerInterface $itemTotalRuleChecker,
        ) 
    ```

### Architectural changes

1. Since catalog promotion action and scope validations have been rewritten to be more inline with symfony, the previous
   abstraction has been deprecated. This includes:
    - `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface`
    - `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface`
    - `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionValidator`
    - `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeValidator`

1. The way of getting variants prices based on options has been changed,
   as such the following services were deprecated, please use their new counterpart.
    * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface`
      use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface`
    * instead of `Sylius\Component\Core\Provider\ProductVariantsPricesProvider`
      use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
    * instead of `Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper`
      use `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsPricesMapProvider`
    * instead of `Sylius\Bundle\CoreBundle\Twig\ProductVariantsPricesExtension`
      use `Sylius\Bundle\CoreBundle\Twig\ProductVariantsMapExtension`

   Subsequently, the `sylius_product_variant_prices` twig function is deprecated, use `sylius_product_variants_map`
   instead.

   To add more data per variant create a service implementing
   the `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantMapProviderInterface` and tag it
   with `sylius.product_variant_data_map_provider`.


1. Interface `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface` has been refactored and
   is now deprecated. It now extends a new
   interface `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionReadInterface`, which contains
   only getter methods.
    - If your services or custom implementations previously relied
      on `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface` for
      read operations, you should now
      use `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionReadInterface` for better clarity and
      separation of concerns.
    - This change is backward compatible as long as your implementations or services were using only the getter methods
      from `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface`. However, if you also
      utilized setter methods, you should
      continue using `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface`.

1. All console commands have been moved from `Command` to `Console\Command`.
   The `Command` namespace is considered deprecated for console commands
   and will only be used for CQRS-related commands starting with Sylius 2.0.
   List of affected classes:
    - `Sylius\Bundle\OrderBundle\Command\RemoveExpiredCartsCommand`
      to `Sylius\Bundle\OrderBundle\Console\Command\RemoveExpiredCartsCommand`
    - `Sylius\Bundle\PromotionBundle\Command\GenerateCouponsCommand`
      to `Sylius\Bundle\PromotionBundle\Console\Command\GenerateCouponsCommand`
    - `Sylius\Bundle\UiBundle\Command\DebugTemplateEventCommand`
      to `Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand`
    - `Sylius\Bundle\UserBundle\Command\AbstractRoleCommand`
      to `Sylius\Bundle\UserBundle\Console\Command\AbstractRoleCommand`
    - `Sylius\Bundle\UserBundle\Command\DemoteUserCommand`
      to `Sylius\Bundle\UserBundle\Console\Command\DemoteUserCommand`
    - `Sylius\Bundle\UserBundle\Command\PromoteUserCommand`
      to `Sylius\Bundle\UserBundle\Console\Command\PromoteUserCommand`
    - `Sylius\Bundle\CoreBundle\Command\Model\PluginInfo`
      to `Sylius\Bundle\CoreBundle\Console\Command\Model\PluginInfo`
    - `Sylius\Bundle\CoreBundle\Command\AbstractInstallCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\AbstractInstallCommand`
    - `Sylius\Bundle\CoreBundle\Command\CancelUnpaidOrdersCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\CancelUnpaidOrdersCommand`
    - `Sylius\Bundle\CoreBundle\Command\CheckRequirementsCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\CheckRequirementsCommand`
    - `Sylius\Bundle\CoreBundle\Command\InformAboutGUSCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\InformAboutGUSCommand`
    - `Sylius\Bundle\CoreBundle\Command\InstallAssetsCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\InstallAssetsCommand`
    - `Sylius\Bundle\CoreBundle\Command\InstallCommand` to `Sylius\Bundle\CoreBundle\Console\Command\InstallCommand`
    - `Sylius\Bundle\CoreBundle\Command\InstallDatabaseCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\InstallDatabaseCommand`
    - `Sylius\Bundle\CoreBundle\Command\InstallSampleDataCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\InstallSampleDataCommand`
    - `Sylius\Bundle\CoreBundle\Command\JwtConfigurationCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\JwtConfigurationCommand`
    - `Sylius\Bundle\CoreBundle\Command\SetupCommand` to `Sylius\Bundle\CoreBundle\Console\Command\SetupCommand`
    - `Sylius\Bundle\CoreBundle\Command\ShowAvailablePluginsCommand`
      to `Sylius\Bundle\CoreBundle\Console\Command\ShowAvailablePluginsCommand`

1. Product variants resolving has been refactored for better extendability.
   The tag `sylius.product_variant_resolver.default` has been removed as it was never used.
   To register a custom variant resolver use the tag `sylius.product_variant_resolver` with a fitting priority.

   All internal usages of service `sylius.product_variant_resolver.default` have been switched
   to `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`, if you have been using the
   `sylius.product_variant_resolver.default` service apply this change accordingly.

1. Sylius 2.0 will introduce a significant restructuring of our class system to enhance efficiency and clarity. The
   changes are as follows:
    - `Message` will be migrated to `Command`.
    - `MessageDispatcher` will be migrated to `CommandDispatcher`.
    - `MessageHandler` will be migrated to `CommandHandler`.
    - Example: Within the `Sylius\Bundle\CoreBundle`, the `MessageHandler\ResendOrderConfirmationEmailHandler` class
      will be migrated to `CommandHandler\ResendOrderConfirmationEmailHandler`. This pattern will be mirrored across
      other bundles in the system.

### Interfaces, Classes and Services

1. Class `Sylius\Component\Core\Promotion\Updater\Rule\TotalOfItemsFromTaxonRuleUpdater`
   has been deprecated, as it is no more used.

1. Class `Sylius\Component\Core\Promotion\Updater\Rule\ContainsProductRuleUpdater`
   has been deprecated, as it is no more used.

1. Class `Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManager` and its
   interface `Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface`
   have been deprecated, use `Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager`
   and `Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface` instead.

1. Class `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManager` and its
   interface `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface`
   have been deprecated, use `Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManager`
   and `Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface` instead.

1. Class `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManager` and its
   interface `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface`
   have been deprecated, use `Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager`
   and `Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface` instead.

1. Class `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManager` and its
   interface `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface`
   have been deprecated, use `Sylius\Bundle\CoreBundle\Mailer\OrderEmailManager`
   and `Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface` instead.

1. Class `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionChoiceType` has been deprecated.
   Use `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionAutocompleteType` instead.

1. Interface `Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface` and
   class `Sylius\Bundle\ShopBundle\Twig\OrderItemsSubtotalExtension` responsible for the `sylius_order_items_subtotal`
   twig function have been deprecated and will be removed in Sylius 2.0.
   Use the `::getItemsSubtotal()` method from the `Order` class instead.

1. Interface `Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface` has been deprecated and
   will be removed in Sylius 2.0.

1. Both `getCreatedByGuest` and `setCreatedByGuest` methods were deprecated
   on `Sylius\Component\Core\Model\OrderInterface`.
   Please use `isCreatedByGuest` instead of the first one. The latter is a part of the `setCustomerWithAuthorization`
   logic and should be used only this way.

1. The `Sylius\Bundle\ShippingBundle\Provider\Calendar` and `Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider`
   have been deprecated and will be removed in Sylius 2.0. Use `Symfony\Component\Clock\Clock` instead.

1. Class `Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker` has been deprecated.
   Use `Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker` instead.

1. Class `Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker` has been deprecated.
   Use `Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker` instead.

1. The `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion` command and its handler
   `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler` have been
   deprecated.
   Use `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion` command instead.

1. Class `Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculator` has been deprecated. Order items subtotal
   calculation
   is now available on the Order model `Sylius\Component\Core\Model\Order::getItemsSubtotal`.

1. The `redirectToCartSummary` protected method of `Sylius\Bundle\OrderBundle\Controller\OrderController` has been
   deprecated as it was never used and will be removed in Sylius 2.0.

1. Due to optimizations of the Order's grid
   the `Sylius\Component\Core\Repository\OrderRepositoryInterface::createSearchListQueryBuilder` method bas been
   deprecated in both the interface and the class, and replaced
   by `Sylius\Component\Core\Repository\OrderRepositoryInterface::createCriteriaAwareSearchListQueryBuilder`.
   Also `Sylius\Component\Core\Repository\OrderRepositoryInterface::createByCustomerIdQueryBuilder` has been deprecated
   in both the interface and the class, and replaced
   by `Sylius\Component\Core\Repository\OrderRepositoryInterface::createByCustomerIdCriteriaAwareQueryBuilder` for the
   same reason. Both changes affect
   `sylius_admin_order` and `sylius_admin_customer_order` grids configuration.

1. The `Sylius\Bundle\CoreBundle\Provider\SessionProvider` has been deprecated and will be removed in Sylius 2.0.

1. The `Sylius\Component\Addressing\Repository\ZoneRepositoryInterface` and
   `Sylius\Bundle\AddressingBundle\Repository\ZoneRepository` were added.
   If you created a custom `Zone` repository, you should update it to extend
   the `Sylius\Bundle\AddressingBundle\Repository\ZoneRepository`

1. The service definition for `sylius.promotion_rule_checker.item_total` has been updated. The class has been changed
   from `Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker`
   to `Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker`.

1. The `sylius.http_message_factory` service has been deprecated. Use `Psr\Http\Message\RequestFactoryInterface`
   instead.

1. The `sylius.http_client` has become an alias to `psr18.http_client` service.

1. The `sylius.payum.http_client` has become a service ID of newly
   created `Sylius\Bundle\PayumBundle\HttpClient\HttpClient`.

1. The argument of `Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener` has been changed
   from `Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface` to `sylius.availability_checker`.
   This is the alias to the same service.

1. The interface `Sylius\Component\Core\SyliusLocaleEvents` has been deprecated and will be removed in Sylius 2.0.

1. The `Sylius\Bundle\CoreBundle\EventListener\CustomerReviewsDeleteListener` has been removed as it was not used.

### Configuration

1. To ease customization we've introduced attributes for some services in `1.13`:
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

1. A new parameter has been added to specify the validation groups for a given promotion action.
   If you have any custom validation groups for your promotion action, you need to add them to
   your `config/packages/_sylius.yaml` file.
   Additionally, if you have your own promotion action and want to add your validation groups, you can add another key
   to the `promotion_action.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\PromotionActionGroupValidator` and it resolves the groups
   based on the type of the passed promotion action.

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
   Along with this update, constraints have been removed from specific action form types. The affected form types
   include:
    - `Sylius\Bundle\PromotionBundle\Form\Type\Action\FixedDiscountConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitFixedDiscountConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitPercentageDiscountConfigurationType`

   The constraints previously defined in these forms are now
   in `src/Sylius/Bundle/CoreBundle/Resources/config/validation/PromotionAction.xml` and managed via the new validation
   groups parameters in the configuration.

1. Sylius Mailer email configuration keys in
   the `src/Sylius/Bundle/CoreBundle/Resources/config/app/sylius/sylius_mailer.yml` file have been changed:

   Deprecated:
    ```yaml
    sylius_mailer:
        emails:
           account_verification_token:
               subject: sylius.emails.user.verification_token.subject
    ```

   New:
    ```yaml
    sylius_mailer:
        emails:
            account_verification:         
                subject: sylius.email.user.account_verification.subject
    ```

1. A new parameter has been added to specify the validation groups for a given catalog promotion action.
   If you have any custom validation groups for your catalog promotion action, you need to add them to
   your `config/packages/_sylius.yaml` file.
   Additionally, if you have your own catalog promotion action and want to add your validation groups, you can add
   another key to the `catalog_promotion_action.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionGroupValidator` and it resolves the
   groups based on the type of the passed catalog promotion action.

    ```yaml
    sylius_promotion:
        catalog_promotion_action:
            validation_groups:
                percentage_discount:
                    - 'sylius'
                    - 'sylius_catalog_promotion_action_percentage_discount'
                fixed_discount:
                    - 'sylius'
                    - 'sylius_catalog_promotion_action_fixed_discount'
                your_action:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```
   Along with this update, constraints have been removed from specific rule form types. The affected form types include:
    - `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\FixedDiscountActionConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType`

   The constraints previously defined in these forms are now
   in `src/Sylius/Bundle/PromotionBundle/Resources/config/validation/CatalogPromotionAction.xml`
   and `src/Sylius/Bundle/CoreBundle/Resources/config/validation/CatalogPromotionAction.xml` and are managed via the new
   validation groups parameters in the configuration.

1. A new parameter has been added to specify the validation groups for a given catalog promotion scope.
   If you have any custom validation groups for your catalog promotion scope, you need to add them to
   your `config/packages/_sylius.yaml` file.
   Additionally, if you have your own catalog promotion scope and want to add your validation groups, you can add
   another key to the `catalog_promotion_scope.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeGroupValidator` and it resolves the
   groups based on the type of the passed catalog promotion scope.

    ```yaml
    sylius_promotion:
        catalog_promotion_scope:
            validation_groups:
                for_products:
                    - 'sylius'
                    - 'sylius_catalog_promotion_scope_for_products'
                for_taxons:
                    - 'sylius'
                    - 'sylius_catalog_promotion_scope_for_taxons'
                your_scope:
                    - 'sylius'
                    - 'your_custom_validation_group'
    ```
   Along with this update, constraints have been removed from specific rule form types. The affected form types include:
    - `Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType`
    - `Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType`
    - `Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType`

   The constraints previously defined in these forms are now
   in `src/Sylius/Bundle/CoreBundle/Resources/config/validation/CatalogPromotionScope.xml` and managed via the new
   validation groups parameters in the configuration.

1. A new parameter has been added to specify the validation groups for a given promotion rule.
   If you have any custom validation groups for your promotion rule, you need to add them to
   your `config/packages/_sylius.yaml` file.
   Additionally, if you have your own promotion rule and want to add your validation groups, you can add another key to
   the `promotion_rule.validation_groups` parameter.
   This is handled by `Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator` and it resolves the groups
   based on the type of the passed promotion rule.

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

   The constraints previously defined in these forms are now
   in `src/Sylius/Bundle/CoreBundle/Resources/config/validation/PromotionRule.xml` and managed via the new validation
   groups parameters in the configuration.

1. A new parameter has been added to specify the validation groups for given gateway factory. If you have any custom
   validation groups for your factory, you need to add them to your `config/packages/_sylius.yaml` file. Also, if you
   have your own gateway factory and want to add your validation groups you can add another entry to
   the `validation_groups` configuration node. It is handled
   by `Sylius\Bundle\PayumBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator` and it resolves the groups
   based on the passed factory name:
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

1. New parameters have been added to specify the validation groups for a given shipping method rule and calculator.
   If you have any custom validation groups for your calculators or rules, you need to add them to
   your `config/packages/_sylius.yaml` file.
   Also, if you have your own shipping method rule or calculator and want to add your validation groups you can add
   another key to the `validation_groups` parameter:

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

1. The new parameter has been added to specify the max integer value that could be used, by default the value is `2147483647` if you want to change it you need to add the following configuration to your `config/packages/_sylius.yaml` file:

    ```yaml
    sylius_core:
        max_int_value: 9223372036854775807
    ```

1. The `sylius_inventory.checker` configuration node has been deprecated and will be removed in 2.0.

1. Due to changes in API paths, the `security` configuration has been changed:
    ```yaml
    security:
       ...
       firewalls:
          new_api_admin_user:
             json_login:
    -           check_path: "%sylius.security.new_api_admin_route%/authentication-token"
    +           check_path: "%sylius.security.new_api_admin_route%/administrators/token"
          ...
          new_api_shop_user:
             json_login:
    -           check_path: "%sylius.security.new_api_shop_route%/authentication-token"
    +           check_path: "%sylius.security.new_api_shop_route%/customers/token"
       ...
       access_control:
       ...
    -     - { path: "%sylius.security.new_api_admin_route%/reset-password-requests", role: PUBLIC_ACCESS }
    +     - { path: "%sylius.security.new_api_admin_route%/administrators/reset-password", role: PUBLIC_ACCESS }
    -     - { path: "%sylius.security.new_api_admin_route%/authentication-token", role: PUBLIC_ACCESS }
    +     - { path: "%sylius.security.new_api_admin_route%/administrators/token", role: PUBLIC_ACCESS }
    -     - { path: "%sylius.security.new_api_shop_route%/authentication-token", role: PUBLIC_ACCESS }
    +     - { path: "%sylius.security.new_api_shop_route%/customers/token", role: PUBLIC_ACCESS }
    ```

1. A new firewall has been added to the `security` configuration:
    ```yaml
    security:
       ...
       firewalls:
          ...
          image_resolver:
            pattern: ^/media/cache/resolve
            security: false
          ...
       ...
    ```

1. All instances of `options` node have been deprecated in all resource and translation configurations.

### State Machine

1. We have configured all existing Sylius graphs to be usable with the Symfony Workflow out of the box.
   Winzou State Machine is still the default state machine for all graphs, but you can switch to Symfony Workflow via
   configuration:

    ```yaml
    sylius_state_machine_abstraction:
        graphs_to_adapters_mapping:
            <graph_name>: <adapter_name>
            # e.g.
            sylius_order_checkout: symfony_workflow # available adapters: symfony_workflow, winzou_state_machine
   
        # we can also can the default adapter
        default_adapter: symfony_workflow # winzou_state_machine is a default value here
    ```
   Starting with `Sylius 2.0` Symfony Workflow will be the default state machine for all graphs.

1. In the `sylius_payment` state machine of `PaymentBundle`, there has been a change in the state name:
    - State name change:
        - From: `void`
        - To: `unknown`

1. In the `sylius_payment` state machine of `PaymentBundle`, a new state `authorized` has been introduced, along with a
   new transition:
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
    - Transition `fail`:
        - From states: [`new`, `processing`, `authorized`]
        - To state: `failed`

### Translations

1. Validation translation key `sylius.review.rating.range` has been replaced by `sylius.review.rating.not_in_range` in
   all places used by Sylius. The `sylius.review.rating.range` has been left for backward compatibility and will be
   removed in Sylius 2.0.

1. Translation keys in the `Sylius\Bundle\CoreBundle\Resources\translations\messages.en.yml` under the `sylius.email`
   key have been changed:

   Deprecated:
    ```yaml
    sylius:
        email:  
            verification_token:
                hello: 'Hello'
                message: 'Verify your account with token: '
                subject: 'Email address verification'
                to_verify_your_email_address: 'To verify your email address - click the link below'
                verify_your_email_address: 'Verify your email address'
    ```

   New:
    ```yaml
    sylius:
        email:
            user:
                account_verification:
                    greeting: 'Hello'
                    message: 'Verify your account with token: '
                    statement: 'Verify your email address'
                    strategy: 'To verify your email address - click the link below'
                    subject: 'Email address verification'
   ```

### Miscellaneous

1. Using Guzzle 6 has been deprecated in favor of Symfony HTTP Client. If you want to still use Guzzle 6 or Guzzle 7,
   you need to install `composer require php-http/guzzle6-adapter` or `composer require php-http/guzzle7-adapter`
   depending on your Guzzle version.
   Subsequently, you need to register the adapter as a `Psr\Http\Client\ClientInterface` service as the following:
    ```yaml
        services:
            Psr\Http\Client\ClientInterface:
                class: Http\Adapter\Guzzle7\Client # for Guzzle 6 use Http\Adapter\Guzzle6\Client instead
    ```

1. PostgreSQL migration support has been introduced. If you are using PostgreSQL, we assume that you have already
   created a database schema in some way.
   All you need to do is run migrations, which will mark all migrations created before Sylius 1.13 as executed.

1. We have explicitly added relationships between product and reviews and between product and attributes in XML
   mappings.
   Because of that, the subscribers `Sylius\Bundle\AttributeBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber`
   and `Sylius\Bundle\ReviewBundle\Doctrine\ORM\Subscriber\LoadMetadataSubscriber` have been modified so they do not add
   a relationship if one already exists. If you have overwritten or decorated it, there may be a need to update it.

1. The behavior of the `sylius:install:setup` command has changed,
   because `Sylius\Bundle\CoreBundle\Installer\Setup\LocaleSetup` has been updated.
   Now, it automatically replaces the existing `locale` parameter in the configuration with the one provided for the
   store.

1. Extracted the section responsible for the `ShopBundle` from `@SyliusCore/Email/accountVerification.html.twig` and
   relocated it to `@SyliusShop/Email/verification.html.twig`.

1. Using `parentId` query parameter to generate slug in `Sylius\Bundle\TaxonomyBundle\Controller\TaxonSlugController`
   has been deprecated.
   Use the `parentCode` query parameter instead.

1. The `Regex` and `Length` constraints have been removed from `Sylius\Component\Addressing\Model\Country`
   in favour of the `Country` constraint.
   Due to that, their translation messages `sylius.country.code.regex` and `sylius.country.code.exact_length` were also removed.

1. The `Regex` and `Length` constraints have been removed from `Sylius\Component\Currency\Model\Currency`
   in favour of the `Currency` constraint.
   Due to that, their translation messages `sylius.currency.regex` and `sylius.currency.exact_length` were also removed.

1. The `Regex` constraint has been removed from `Sylius\Component\Locale\Model\Locale`
   in favour of the `Locale` constraint.
   Due to that, the translation message `sylius.locale.code.regex` was also removed.

1. The `sylius_admin_ajax_taxon_move` route has been deprecated. If you're relaying on it, consider migrating to new
   `sylius_admin_ajax_taxon_move_up` and `sylius_admin_ajax_taxon_move_down` routes.

1. Check if the `countryCode` is set has been removed from the `Sylius\Component\Addressing\ModelAddress::setProvinceCode` 
   method. The adequate check is ensured by validation.

1. The migration changing the type of the `DC2TYPE:array` to `JSON` has been added to the following fields in tables:
    - `data` => `sylius_address_log_entries`
    - `roles` => `sylius_admin_user`
    - `configuration` => `sylius_catalog_promotion_action`
    - `configuration` => `sylius_catalog_promotion_scope`
    - `configuration` => `sylius_product_attribute`
    - `configuration` => `sylius_promotion_action`
    - `configuration` => `sylius_promotion_rule`
    - `configuration` => `sylius_shipping_method`
    - `configuration` => `sylius_shipping_method_rule`
    - `roles` => `sylius_shop_user`

   If you prefer skipping this migration and applying the changes yourself you can do it by executing the following commands:

   For MySQL migration:
    ```bash
      bin/console doctrine:migrations:version --add Sylius\\Bundle\\CoreBundle\\Migrations\\Version20240315112656
    ```
   For PostgresSQL migration:
    ```bash
      bin/console doctrine:migrations:version --add Sylius\\Bundle\\CoreBundle\\Migrations\\Version20240318094743
    ```

   Along with this change, the `data` field in the `sylius_address_log_entries` table is now nullable within our database schema, aligning with the nullable nature of the corresponding field in Gedmo's LogEntry.
