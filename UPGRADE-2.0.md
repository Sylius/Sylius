# UPGRADE FROM `2.0.5` TO `2.0.6`

### Behat

1. The context classes have been refactored. All `UI` occurrences of the following step definitions have been removed:

- `@When I save my changes`
- `@When I try to save my changes`

These have been replaced by the new common context class: `Sylius\Behat\Context\Ui\SaveContext`
identified by the id `sylius.behat.context.ui.save`.

If you use any of the removed methods, please update your suite configuration to include the new context class:
```
your_behat_profile:
    suites:
        your_suite_name:
            contexts:
                - ...
+               - sylius.behat.context.ui.save
```

# UPGRADE FROM `2.0.2` TO `2.0.3`

1. New `left` and `right` sections have been added to the following Twig hooks to improve customization:
    - `sylius_admin.admin_user.create.content.form.sections`
    - `sylius_admin.admin_user.update.content.form.sections`
    - `sylius_admin.customer.create.content.form.sections`
    - `sylius_admin.customer.update.content.form.sections`

    Hooks previously attached to the `sections` hook are now divided into `left` and `right` sections. This change doesn't affect to the original hook names. 

    For example, the `account` hook previously attached to `sylius_admin.admin_user.create.content.form.sections` is now located in `sylius_admin.admin_user.create.content.form.sections#left`,
    but its name remains `sylius_admin.admin_user.create.content.form.sections.account`.

# UPGRADE FROM `2.0.0` TO `2.0.1`

### Frontend

1. Flags have been removed from languages - it resolves all ambiguity and ensures a consistent and straightforward language 
   selection experience. This approach reduces maintenance complexity and eliminates the risk of incorrect 
   or misleading flag assignments.

# UPGRADE FROM `1.14` TO `2.0`

## To start off

Even if your app is barely customized, it will require some manual adjustments before it can run again. Depending on
whether you use Symfony Flex, some of these changes may be applied automatically, but it’s important to check them
manually regardless.

* Packages configuration changes:

```diff
# config/packages/_sylius.yaml

imports:
...
+   - { resource: "@SyliusPayumBundle/Resources/config/app/config.yaml" }

...

sylius_payment:
    resources:
+       gateway_config:
+           classes:
+               model: App\Entity\Payment\GatewayConfig

...

sylius_payum:
    resources:
-       gateway_config:
-           classes:
-               model: App\Entity\Payment\GatewayConfig
```

* API firewalls have been renamed and user checkers have been configured on them
  with `security.user_checker.chain.<firewall>` service:

```diff
# config/packages/security.yaml

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

* Routing changes (note that these shop routes are not localized with the prefix: /{_locale} configuration entry):

Shop:

```diff
# config/routes/sylius_shop.yaml

sylius_shop_payum:
-   resource: "@SyliusShopBundle/Resources/config/routing/payum.yml"
+   resource: "@SyliusPayumBundle/Resources/config/routing/integrations/sylius_shop.yaml"

sylius_payment_notify:
+   resource: "@SyliusPaymentBundle/Resources/config/routing/integrations/sylius.yaml"

```

API:

```diff
# config/routes/sylius_api.yaml

sylius_api:
    resource: "@SyliusApiBundle/Resources/config/routing.yml"
-   prefix: "%sylius.security.new_api_route%"
+   prefix: "%sylius.security.api_route%"

```

* Bundle configuration changes:

```diff
# config/bundles.php

<?php

return [
-   Sylius\Calendar\SyliusCalendarBundle::class => ['all' => true],
-   winzou\Bundle\StateMachineBundle\winzouStateMachineBundle::class => ['all' => true],
-   Bazinga\Bundle\HateoasBundle\BazingaHateoasBundle::class => ['all' => true],
-   JMS\SerializerBundle\JMSSerializerBundle::class => ['all' => true],
-   FOS\RestBundle\FOSRestBundle::class => ['all' => true],
-   ApiPlatform\Core\Bridge\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
-   SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class => ['all' => true],
+   ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
+   Sylius\TwigHooks\SyliusTwigHooksBundle::class => ['all' => true],
+   Symfony\UX\TwigComponent\TwigComponentBundle::class => ['all' => true],
+   Symfony\UX\StimulusBundle\StimulusBundle::class => ['all' => true],
+   Symfony\UX\LiveComponent\LiveComponentBundle::class => ['all' => true],
+   Symfony\UX\Autocomplete\AutocompleteBundle::class => ['all' => true],
];

* New Symfony/Messenger transports for handling payment requests have been added. 
Therefore, you need to add the following configuration to your .env file:

```diff
###> symfony/messenger ###
...
+ SYLIUS_MESSENGER_TRANSPORT_PAYMENT_REQUEST_DSN=sync://
+ SYLIUS_MESSENGER_TRANSPORT_PAYMENT_REQUEST_FAILED_DSN=sync://
###< symfony/messenger ###
```

### Migrations

Doctrine migrations have been regenerated, meaning all previous migration files have been removed and their content 
is now in a single migration file. To apply the new migration and get rid of the old entries run migrations as usual:

```bash
    bin/console doctrine:migrations:migrate --no-interaction
```

### PHP support

Sylius 2.0 comes with a bump of minimum supported PHP version to 8.2. 

### Symfony support

The support of Symfony 5.4 has been dropped.
Sylius 2.0 supports both Symfony 6.4 and 7.1

## Main Update

Once you’ve applied these initial changes, your app should be able to run. However, depending on the customizations
you’ve made, you may need to make some additional adjustments. Carefully review the following changes and apply them to
your app as necessary.

### Dependencies

#### Replaced:

* The `swiftmailer/swiftmailer` dependency has been removed. Use `symfony/mailer` instead.

#### Moved:

* The `sylius/theme-bundle` dependency has been moved from CoreBundle to ShopBundle.

#### Removed:

* Removed from main composer.json:

    * `payum/paypal-express-checkout-nvp`
    * `payum/stripe`
    * `stripe/stripe-php`

* Removed from bundles:

    * `AdminBundle`:
        * `twig/intl-extra`
    * `ApiBundle`:
        * `sylius/payum-bundle`
    * `AttributeBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
    * `ChannelBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
    * `CoreBundle`:
        * `sylius/payum-bundle`
        * `sylius/theme-bundle`
        * `sylius/ui-bundle`
        * `symfony/templating`
        * `jms/serializer-bundle`
        * `sonata-project/block-bundle`
        * `sylius-labs/polyfill-symfony-framework-bundle`
    * `CurrencyBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
        * `symfony/templating`
    * `InventoryBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
        * `symfony/templating`
    * `LocaleBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
        * `symfony/templating`
    * `MoneyBundle`:
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
        * `symfony/templating`
    * `OrderBundle`:
        * `sylius-labs/polyfill-symfony-framework-bundle`
        * `symfony/templating`
    * `PayumBundle`:
        * `sylius/core`
    * `ProductBundle`
        * `friendsofsymfony/rest-bundle`
        * `jms/serializer-bundle`
    * `PromotionBundle`
        * `sylius/calendar`
    * `ShippingBundle`
        * `sylius/calendar`
    * `ShopBundle`
        * `twig/intl-extra`
    * `TaxationBundle`
        * `sylius/calendar`
    * `UiBundle`
        * `sonata-project/block-bundle`
        * `sylius-labs/polyfill-symfony-event-dispatcher`
        * `sylius-labs/polyfill-symfony-framework-bundle`
        * `symfony/templating`
    * `UserBundle`
        * `sylius-labs/polyfill-symfony-event-dispatcher`
        * `sylius-labs/polyfill-symfony-framework-bundle`

#### Optional

* Winzou State Machine repositories have been moved to the suggested section of composer.json,
  if you still want to use them, install the following:

    * `winzou/state-machine`
    * `winzou/state-machine-bundle`

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
    -       private FormatMoneyHelperInterface|MoneyFormatterInterface $helper,
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

   `Sylius\Bundle\PayumBundle\Form\Extension\CryptedGatewayConfigTypeExtension`
    ```diff
    + use Sylius\Bundle\PayumBundle\Checker\PayumGatewayConfigEncryptionCheckerInterface;

        public function __construct(
    +       private readonly PayumGatewayConfigEncryptionCheckerInterface $encryptionChecker,
            private ?CypherInterface $cypher = null,
        )
    ```
   
    `Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType`
    ```diff
        public function __construct(
    +       private readonly AddressComparatorInterface $addressComparator,
            string $dataClass,
            array $validationGroups = []
    -       private readonly AddressComparatorInterface $addressComparator = null,
        )
    ```

   `Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor`
    ```diff
        public function __construct(
            private OrderPaymentProviderInterface $orderPaymentProvider,
    -       private string $targetState = PaymentInterface::STATE_CART,
            private OrderPaymentsRemoverInterface $orderPaymentsRemover,
            private array $unprocessableOrderStates,
    +       private string $targetState = PaymentInterface::STATE_CART,
        )
    ```

   `Sylius\Bundle\AdminBundle\Action\ResendOrderConfirmationEmailAction`
    ```diff
    use Symfony\Component\Routing\RouterInterface;

        public function __construct(
            private OrderRepositoryInterface $orderRepository,
    -       private OrderEmailManagerInterface|ResendOrderConfirmationEmailDispatcherInterface $orderEmailManager,
    +       private ResendOrderConfirmationEmailDispatcherInterface $resendOrderConfirmationEmailDispatcher,
            private CsrfTokenManagerInterface $csrfTokenManager,
    -       private RequestStack|SessionInterface $requestStackOrSession,
    +       private RequestStack $requestStack,
    -       private ?RouterInterface $router = null,
    +       private RouterInterface $router,
        )
    ```

    `Sylius\Bundle\AdminBundle\Action\ResendShipmentConfirmationEmailAction`
    ```diff
    use Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface;
    use Sylius\Bundle\CoreBundle\MessageDispatcher\ResendShipmentConfirmationEmailDispatcherInterface;
    
        public function __construct(
            private ShipmentRepositoryInterface $shipmentRepository,
    -       private ResendShipmentConfirmationEmailDispatcherInterface|ShipmentEmailManagerInterface $shipmentEmailManager,
    +       private ResendShipmentConfirmationEmailDispatcherInterface $resendShipmentConfirmationDispatcher,
            private CsrfTokenManagerInterface $csrfTokenManager,
    -       private RequestStack|SessionInterface $requestStackOrSession,
    +       private RequestStack $requestStack,
        )
    ```

    `Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController`
    ```diff
    use Symfony\Component\Routing\RouterInterface;
    
        public function __construct(
            private UserImpersonatorInterface $impersonator,
            private AuthorizationCheckerInterface $authorizationChecker,
            private UserProviderInterface $userProvider,
    -       ?RouterInterface $router,
    +       private RouterInterface $router,
            private string $authorizationRole,
        )
    ```

    `Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface;
    
        public function __construct(
    -       private DeprecatedShipmentEmailManagerInterface|ShipmentEmailManagerInterface $shipmentEmailManager
    +       private ShipmentEmailManagerInterface $shipmentEmailManager
        )
    ```

   `Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration\SelectAttributeChoicesCollectionType`
    ```diff
    use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

        public function __construct(
    -       ?TranslationLocaleProviderInterface $localeProvider = null,
        )
    ```

    `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
    ```diff
    use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
    use Symfony\Component\Messenger\MessageBusInterface;
    
        public function __construct(
            private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    -       private CatalogPromotionRemovalAnnouncerInterface|MessageBusInterface $catalogPromotionRemovalAnnouncer,
    -       private ?MessageBusInterface $eventBus = null,
    +       private CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        )
    ```

    `Sylius\Bundle\CoreBundle\Fixture\Factory\ProductExampleFactory`
    ```diff
    use Symfony\Component\Config\FileLocatorInterface;
    
        public function __construct(
            private FactoryInterface $productFactory,
            private FactoryInterface $productVariantFactory,
            private FactoryInterface $channelPricingFactory,
            private ProductVariantGeneratorInterface $variantGenerator,
            private FactoryInterface $productAttributeValueFactory,
            private FactoryInterface $productImageFactory,
            private FactoryInterface $productTaxonFactory,
            private ImageUploaderInterface $imageUploader,
            private SlugGeneratorInterface $slugGenerator,
            private RepositoryInterface $taxonRepository,
            private RepositoryInterface $productAttributeRepository,
            private RepositoryInterface $productOptionRepository,
            private RepositoryInterface $channelRepository,
            private RepositoryInterface $localeRepository,
    -       private ?RepositoryInterface $taxCategoryRepository = null,
    -       private ?FileLocatorInterface $fileLocator = null,
    +       private RepositoryInterface $taxCategoryRepository,
    +       private FileLocatorInterface $fileLocator,
        )
    ```

    `Sylius\Bundle\CoreBundle\EventListener\PaymentPreCompleteListener`
    ```diff
        public function __construct(
    +       private OrderItemAvailabilityCheckerInterface|AvailabilityCheckerInterface $availabilityChecker,
    -       private AvailabilityCheckerInterface $availabilityChecker,
        )
    ```

    `Sylius\Bundle\CoreBundle\Fixture\Factory\PromotionExampleFactory`
    ```diff
    use Sylius\Component\Resource\Repository\RepositoryInterface;
    
        public function __construct(
            private FactoryInterface $promotionFactory,
            private ExampleFactoryInterface $promotionRuleExampleFactory,
            private ExampleFactoryInterface $promotionActionExampleFactory,
            private ChannelRepositoryInterface $channelRepository,
    -       private ?FactoryInterface $couponFactory = null,
    -       private ?RepositoryInterface $localeRepository = null,
    +       private FactoryInterface $couponFactory,
    +       private RepositoryInterface $localeRepository,
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
            private RepositoryInterface $localeRepository,
            private FactoryInterface $localeFactory,
            private string $locale,
    -       private ?Filesystem $filesystem = null,
    -       private ?string $localeParameterFilePath,
    +       private Filesystem $filesystem,
    +       private string $localeParameterFilePath,
        )
    ```

    `Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator`
    ```diff
    use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
    
        public function __construct(
            private ManagerRegistry $registry,
    -       private ?PropertyAccessorInterface $accessor = null,
    +       private PropertyAccessorInterface $accessor,
        )
    ```

    `Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\OrderEmailManagerInterface;
    use Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface as DeprecatedOrderEmailManagerInterface;
    
        public function __construct(
    -       private DeprecatedOrderEmailManagerInterface|OrderEmailManagerInterface $orderEmailManager
    +       private OrderEmailManagerInterface $orderEmailManager
        )
    ```

    `Sylius\Bundle\ShopBundle\Controller\ContactController`
    ```diff
    use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
    use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface as DeprecatedContactEmailManagerInterface;
    
        public function __construct(
            private RouterInterface $router,
            private FormFactoryInterface $formFactory,
            private Environment $templatingEngine,
            private ChannelContextInterface $channelContext,
            private CustomerContextInterface $customerContext,
            private LocaleContextInterface $localeContext,
    -       private ContactEmailManagerInterface|DeprecatedContactEmailManagerInterface $contactEmailManager,
    +       private ContactEmailManagerInterface $contactEmailManager,
        )
    ```

    `Sylius\Component\Addressing\Matcher\ZoneMatcher`
    ```diff
    use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
    use Sylius\Component\Resource\Repository\RepositoryInterface;
    
        public function __construct(
    -       private RepositoryInterface|ZoneRepositoryInterface $zoneRepository
    +       private ZoneRepositoryInterface $zoneRepository
        )
    ```

    `Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater`
    ```diff
    use Doctrine\Persistence\ObjectManager;
    use SM\Factory\Factory;
    use Sylius\Abstraction\StateMachine\StateMachineInterface;
    
        public function __construct(
            private OrderRepositoryInterface $orderRepository,
    -       private Factory|StateMachineInterface $stateMachineFactory,
    +       private StateMachineInterface $stateMachine,
            private string $expirationPeriod,
    -       private ?LoggerInterface $logger = null,
    -       private ?ObjectManager $orderManager = null,
    +       private LoggerInterface $logger,
    +       private ObjectManager $orderManager,
            private int $batchSize = 100,
        )
    ```

    `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
    ```diff
    use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
    
        public function __construct(
    -       private ?ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker = null,
    +       private ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        )
    ```

    `Sylius\Component\Core\Taxation\Applicator\OrderItemsTaxesApplicator`
    ```diff
    use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
    
        public function __construct(
            private CalculatorInterface $calculator,
            private AdjustmentFactoryInterface $adjustmentFactory,
            private IntegerDistributorInterface $distributor,
            private TaxRateResolverInterface $taxRateResolver,
    -       private ?ProportionalIntegerDistributorInterface $proportionalIntegerDistributor = null,
    +       private ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
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

    `Sylius\Component\Product\Resolver\DefaultProductVariantResolver`
    ```diff
        public function __construct(
    -       private ?ProductVariantRepositoryInterface $productVariantRepository = null
    +       private readonly ProductVariantRepositoryInterface $productVariantRepository
        )
    ```

    `Sylius\Bundle\CoreBundle\EventListener\TaxonDeletionListener`
    ```diff
        public function __construct(
    -       private RequestStack|SessionInterface $requestStackOrSession,
    +       private RequestStack $requestStack,
            private ChannelRepositoryInterface $channelRepository,
            private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
            TaxonAwareRuleUpdaterInterface ...$ruleUpdaters,
        )
    ```

    `Sylius\Component\Core\Cart\Context\ShopBasedCartContext`
    ```diff
       public function __construct(
           private CartContextInterface $cartContext,
           private ShopperContextInterface $shopperContext,
    -      private ?CreatedByGuestFlagResolverInterface $createdByGuestFlagResolver = null,
    +      private CreatedByGuestFlagResolverInterface $createdByGuestFlagResolver,
       )
    ```

    `Sylius\Component\Core\Uploader\ImageUploader`
    ```diff
        public function __construct(
    -       protected FilesystemAdapterInterface|FilesystemInterface $filesystem,
    -       protected ?ImagePathGeneratorInterface $imagePathGenerator = null,
    +       protected readonly FilesystemAdapterInterface $filesystem,
    +       protected readonly ImagePathGeneratorInterface $imagePathGenerator,
        )
    ```

    `Sylius\Component\Taxation\Resolver\TaxRateResolver`
    ```diff
        public function __construct(
            protected RepositoryInterface $taxRateRepository,
    -       protected ?TaxRateDateEligibilityCheckerInterface $taxRateDateChecker = null,
    +       protected TaxRateDateEligibilityCheckerInterface $taxRateDateChecker,
        )
    ```

    `Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType`
    ```diff
        public function __construct(
            private RepositoryInterface $zoneRepository,
    -       private array $scopeTypes = []
    +       private readonly array $scopeTypes,
        )
    ```

    `Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator`
    ```diff
        public function __construct(
    -       private ProductVariantPriceCalculatorInterface|ProductVariantPricesCalculatorInterface $productVariantPriceCalculator
    +       private ProductVariantPricesCalculatorInterface $productVariantPricesCalculator
        )
    ```

    `Sylius\Bundle\AdminBundle\Action\Account\RenderResetPasswordPageAction`
    ```diff
        public function __construct(
            private UserRepositoryInterface $userRepository,
            private FormFactoryInterface $formFactory,
    -       private FlashBagInterface|RequestStack $requestStackOrFlashBag,
    +       private RequestStack $requestStack,
            private RouterInterface $router,
            private Environment $twig,
            private string $tokenTtl,
        )
    ```

    `Sylius\Bundle\AdminBundle\Action\Account\ResetPasswordAction`
    ```diff
        public function __construct(
            private FormFactoryInterface $formFactory,
            private ResetPasswordDispatcherInterface $resetPasswordDispatcher,
    -       private FlashBagInterface|RequestStack $requestStackOrFlashBag,
    +       private RequestStack $requestStack,
            private RouterInterface $router,
            private Environment $twig,
        )
    ```

    `Sylius\Bundle\AdminBundle\Event\OrderShowMenuBuilderEvent`
    ```diff
        public function __construct(
            FactoryInterface $factory,
            ItemInterface $menu,
            private OrderInterface $order,
    -       private StateMachineInterface|WinzouStateMachineInterface $stateMachine,
    +       private StateMachineInterface $stateMachine,
        )
    ```

    `Sylius\Bundle\AttributeBundle\Form\Type\AttributeValueType`
    ```diff
        public function __construct(
            string $dataClass,
            array $validationGroups,
            protected string $attributeChoiceType,
            protected RepositoryInterface $attributeRepository,
            protected RepositoryInterface $localeRepository,
            protected FormTypeRegistryInterface $formTypeRegistry,
    -       protected ?DataTransformerInterface $localeToCodeTransformer = null,
    +       protected DataTransformerInterface $localeToCodeTransformer,
        )
    ```

    `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor`
    ```diff
        public function __construct(
            private CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    -       private CatalogPromotionRemovalAnnouncerInterface|MessageBusInterface $catalogPromotionRemovalAnnouncer,
    -       private ?MessageBusInterface $eventBus = null, /** @phpstan-ignore-line */
    +       private CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        )
    ```

    `Sylius\Bundle\CoreBundle\Fixture\Factory\AdminUserExampleFactory`
    ```diff
        public function __construct(
            private FactoryInterface $userFactory,
            private string $localeCode,
    -       private ?FileLocatorInterface $fileLocator = null,
    -       private ?ImageUploaderInterface $imageUploader = null,
    -       private ?FactoryInterface $avatarImageFactory = null,
    +       private FileLocatorInterface $fileLocator,
    +       private ImageUploaderInterface $imageUploader,
    +       private FactoryInterface $avatarImageFactory,
        )
    ```

    `Sylius\Bundle\CoreBundle\Fixture\Factory\ChannelExampleFactory`
    ```diff
        public function __construct(
            private ChannelFactoryInterface $channelFactory,
            private RepositoryInterface $localeRepository,
            private RepositoryInterface $currencyRepository,
            private RepositoryInterface $zoneRepository,
    -       ?TaxonRepositoryInterface $taxonRepository = null,
    -       ?FactoryInterface $shopBillingDataFactory = null,
    +       private TaxonRepositoryInterface $taxonRepository,
    +       private FactoryInterface $shopBillingDataFactory,
        )
    ```

    `Sylius\Bundle\CoreBundle\Fixture\Factory\ShippingMethodExampleFactory`
    ```diff
        public function __construct(
            private FactoryInterface $shippingMethodFactory,
            private RepositoryInterface $zoneRepository,
            private RepositoryInterface $shippingCategoryRepository,
            private RepositoryInterface $localeRepository,
            private ChannelRepositoryInterface $channelRepository,
    -       private ?RepositoryInterface $taxCategoryRepository = null,
    +       private RepositoryInterface $taxCategoryRepository,
        )
    ```

    `Sylius\Bundle\CoreBundle\Installer\Requirement\FilesystemRequirements`
    ```diff
        public function __construct(
            TranslatorInterface $translator,
            string $cacheDir,
            string $logsDir,
    -       ?string $rootDir = null
        )
    ```

    `Sylius\Bundle\CoreBundle\Twig\CheckoutStepsExtension`
    ```diff
        public function __construct(
    -       private readonly CheckoutStepsHelper|OrderPaymentMethodSelectionRequirementCheckerInterface $checkoutStepsHelper,
    -       private readonly ?OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker = null,
    +       private readonly OrderPaymentMethodSelectionRequirementCheckerInterface $orderPaymentMethodSelectionRequirementChecker,
    +       private readonly OrderShippingMethodSelectionRequirementCheckerInterface $orderShippingMethodSelectionRequirementChecker,
        )
    ```

    `Sylius\Bundle\CoreBundle\Twig\PriceExtension`
    ```diff
        public function __construct(
    -       private readonly PriceHelper|ProductVariantPricesCalculatorInterface $helper
    +       private readonly ProductVariantPricesCalculatorInterface $productVariantPricesCalculator
        )
    ```

    `Sylius\Bundle\CoreBundle\Twig\VariantResolverExtension`
    ```diff
        public function __construct(
    -       private readonly ProductVariantResolverInterface|VariantResolverHelper $helper
    +       private readonly ProductVariantResolverInterface $productVariantResolver
        )
    ```

    `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueChoiceType`
    ```diff
        public function __construct(
    -       ?AvailableProductOptionValuesResolverInterface $availableProductOptionValuesResolver = null
    +       private readonly AvailableProductOptionValuesResolverInterface $availableProductOptionValuesResolver
        )
    ```

    `Sylius\Bundle\ShopBundle\Controller\ContactController`
    ```diff
        public function __construct(
            private RouterInterface $router,
            private FormFactoryInterface $formFactory,
            private Environment $templatingEngine,
            private ChannelContextInterface $channelContext,
            private CustomerContextInterface $customerContext,
            private LocaleContextInterface $localeContext,
    -       private ContactEmailManagerInterface|DeprecatedContactEmailManagerInterface $contactEmailManager,
    +       private ContactEmailManagerInterface $contactEmailManager,
        )
    ```

    `Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener`
    ```diff
        public function __construct(
    -       private DeprecatedOrderEmailManagerInterface|OrderEmailManagerInterface $orderEmailManager
    +       private OrderEmailManagerInterface $orderEmailManager
        )
    ```

    `Sylius\Bundle\UserBundle\Controller\SecurityController`
    ```diff
        public function __construct(
    -       private ?AuthenticationUtils $authenticationUtils = null,
    -       private ?FormFactoryInterface $formFactory = null,
    +       private AuthenticationUtils $authenticationUtils,
    +       private FormFactoryInterface $formFactory,
    +       private Environment $twig,
        )
    ```

    `Sylius\Component\Core\Calculator\ProductVariantPriceCalculator`
    ```diff
        public function __construct(
    -       private ?ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker = null,
    +       private ProductVariantLowestPriceDisplayCheckerInterface $productVariantLowestPriceDisplayChecker,
        )
    ```

    `Sylius\Component\Core\Factory\ChannelFactory`
    ```diff
        public function __construct(
            private FactoryInterface $decoratedFactory,
            private string $defaultCalculationStrategy,
    -       private ?FactoryInterface $channelPriceHistoryConfigFactory = null,
    +       private FactoryInterface $channelPriceHistoryConfigFactory,
        )
    ```

    `Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker`
    ```diff
        public function __construct(
    -       private ?RuleCheckerInterface $itemTotalRuleChecker = null
        )
    ```

    `Sylius\Component\Core\Provider\TranslationLocaleProvider`
    ```diff
        public function __construct(
    -       private LocaleCollectionProviderInterface|RepositoryInterface $localeRepository,
    +       private LocaleCollectionProviderInterface $localeRepository,
            private string $defaultLocaleCode,
        )
    ```

    `Sylius\Component\Core\Translation\TranslatableEntityLocaleAssigner`
    ```diff
        public function __construct(
            private LocaleContextInterface $localeContext,
            private TranslationLocaleProviderInterface $translationLocaleProvider,
    -       private ?CLIContextCheckerInterface $commandBasedChecker = null,
    +       private CLIContextCheckerInterface $commandBasedChecker,
        )
    ```

1. Changes across the codebase:

    ```diff
    -   private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory,
    +   private StateMachineInterface $stateMachine,
    ```
   
    ```diff
    -   private FactoryInterface|StateMachineInterface $stateMachineFactory,
    +   private StateMachineInterface $stateMachine,
    ```

    ```diff
    -   private RequestStack|SessionInterface $requestStackOrSession,
    +   private RequestStack $requestStack,
    ```
   
    ```diff
    + use Symfony\Component\Clock\ClockInterface;
    -   private DateTimeProviderInterface $calendar
    +   private ClockInterface $clock
    ```
   
    ```diff
    -   private ?ProportionalIntegerDistributorInterface $proportionalIntegerDistributor = null,
    +   private ProportionalIntegerDistributorInterface $proportionalIntegerDistributor,
    ```

### Configuration Changes

#### Messenger

- Removed `sylius_default.bus` and `sylius_event.bus` configurations.  
  Use `sylius.command_bus` and `sylius.event_bus` for commands and events, respectively.

#### Sylius State Machine Abstraction

- Removed `sylius_core.state_machine` configuration parameter.
- Changed `sylius_state_machine_abstraction.default_adapter` from `winzou_state_machine` to `symfony_workflow`.

#### Resource

- Removed configuration nodes for resource options `sylius_*.resources.**.options`, such as `sylius_addressing.resources.address.options`.
- Removed `sylius_inventory.checker` configuration node.

#### Autoconfiguration

- Removed `sylius_core.autoconfigure_with_attributes` and `sylius_order.autoconfigure_with_attributes`.  
  Use the following attributes instead of interfaces for autoconfiguration:
    - `Sylius\Bundle\OrderBundle\Attribute\AsCartContext`
    - `Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor`

#### User Resetting Pin

- Removed `sylius_user.resources.{name}.user.resetting.pin` configuration parameter.  
  Due to that the related logic has also been removed, this includes:
    - `reset_password_pin` email
    - `Sylius\Bundle\UserBundle\Controller\UserController::requestPasswordResetPinAction` method
    - `sylius.{user_type}_user.pin_generator.password_reset` services
    - `sylius.{user_type}_user.pin_uniqueness_checker.password_reset` services

#### Parameter Removals and Renaming

- Removed:
    - `sylius.mongodb_odm.repository.class`
    - `sylius.phpcr_odm.repository.class`
    - `sylius.mailer.templates`
- Renamed:
    - `sylius.message.admin_user_create.validation_groups` to `sylius_admin.command_handler.create_admin_user.validation_groups`

#### File Relocations

- The state machine configurations of `PaymentBundle` have been moved and renamed:
  
Winzou state machine:
```diff
- `@SyliusPaymentBundle/Resources/config/app/state_machine.yml`
+ `@SyliusPaymentBundle/Resources/config/app/state_machine/sylius_payment.yaml`
```

Symfony workflow:
```diff
- `@SyliusPaymentBundle/Resources/config/workflow/state_machine.yaml`
+ `@SyliusPaymentBundle/Resources/config/app/workflow/sylius_[resource].yaml`
```

#### Zone Validation Groups

- Added a new parameter to specify validation groups for zones.  
  Configure custom validation groups for zone members in `config/packages/_sylius.yaml`.  
  Example:

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

#### LiipImagineBundle Default Resolver and Loader

- Changed the default resolver and loader names for `LiipImagineBundle` from **default** to **sylius_image** ([reference](https://github.com/Sylius/Sylius/pull/12543)).  
  To modify these defaults, configure `cache` and/or `data_loader` parameters under the `liip_imagine` key.

#### Grids

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

#### Password Encoder & Salt

The encoder and salt have been removed from the User entities. The password hasher configured on Symfony security configuration is in use instead.

This "encoder" is configured via
the [Symfony security password hasher](https://symfony.com/doc/current/security/passwords.html#configuring-a-password-hasher).

For example:

```yaml
# config/packages/security.yaml
security:
    # ...

    password_hashers:
        Sylius\Component\User\Model\UserInterface: argon2i
```

Also, check if you have an encoder configured in the `sylius_user` package configuration.

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
In modern Symfony projects, the hasher name is stored along with the password.

Example:
`$argon2i$v=19$m=65536,t=4,p=1$VVJuMnpUUWhRY1daN1ppMA$2Tx6l3I+OUx+PUPn+vZz1jI3Z6l6IHh2kpG0NdpmYWE`

If some of your users do not have the hasher name stored in the password field you may need to configure the
"migrate_from" option by following the documentation:
https://symfony.com/doc/current/security/passwords.html#configure-a-new-hasher-using-migrate-from

Note:
If your app never changed the hasher name configuration, you don't need to configure this "migrate_from" configuration.

#### Routes

* The following routes have been removed:
    * `sylius_admin_dashboard_statistics`
    * `sylius_admin_ajax_all_product_variants_by_codes`
    * `sylius_admin_ajax_all_product_variants_by_phrase`
    * `sylius_admin_ajax_customer_group_by_code`
    * `sylius_admin_ajax_customer_groups_by_phrase`
    * `sylius_admin_ajax_find_product_options`
    * `sylius_admin_ajax_generate_product_slug`
    * `sylius_admin_ajax_generate_taxon_slug`
    * `sylius_admin_ajax_product_by_code`
    * `sylius_admin_ajax_product_by_name_phrase`
    * `sylius_admin_ajax_product_index`
    * `sylius_admin_ajax_product_options_by_phrase`
    * `sylius_admin_ajax_products_by_phrase`
    * `sylius_admin_ajax_product_variants_by_codes`
    * `sylius_admin_ajax_product_variants_by_phrase`
    * `sylius_admin_ajax_taxon_by_code`
    * `sylius_admin_ajax_taxon_by_name_phrase`
    * `sylius_admin_ajax_taxon_leafs`
    * `sylius_admin_ajax_taxon_root_nodes`
    * `sylius_admin_dashboard_statistics`
    * `sylius_admin_get_attribute_types`
    * `sylius_admin_get_payment_gateways`
    * `sylius_admin_get_product_attributes`
    * `sylius_admin_partial_address_log_entry_index`
    * `sylius_admin_partial_catalog_promotion_show`
    * `sylius_admin_partial_channel_index`
    * `sylius_admin_partial_customer_latest`
    * `sylius_admin_partial_customer_show`
    * `sylius_admin_partial_order_latest`
    * `sylius_admin_partial_order_latest_in_channel`
    * `sylius_admin_partial_product_show`
    * `sylius_admin_partial_promotion_show`
    * `sylius_admin_partial_taxon_show`
    * `sylius_admin_partial_taxon_tree`
    * `sylius_admin_render_attribute_forms`
    * `sylius_shop_ajax_cart_add_item`
    * `sylius_shop_ajax_cart_item_remove`
    * `sylius_shop_ajax_user_check_action`
    * `sylius_shop_partial_cart_summary`
    * `sylius_shop_partial_cart_add_item`
    * `sylius_shop_partial_channel_menu_taxon_index`
    * `sylius_shop_partial_product_association_show`
    * `sylius_shop_partial_product_index_latest`
    * `sylius_shop_partial_product_review_latest`
    * `sylius_shop_partial_product_show_by_slug`
    * `sylius_shop_partial_taxon_index_by_code`
    * `sylius_shop_partial_taxon_show_by_slug`

### Services, classes and interfaces changes

#### Container

In Sylius 2.0, we have changed the visibility of services to `private` by default. This change enhances the performance 
and maintainability of the application and also follows Symfony's best practices for service encapsulation.

**Exceptions:**
- Services required by Symfony to be `public` (e.g., controllers, event listeners) remain public.
- Services used in `ResourceController` must be `public` as they are accessed directly from the container.

#### Classes and Interfaces

1. Removed

* `Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener`
* `Sylius\Bundle\ProductBundle\Controller\ProductSlugController`
* `Sylius\Bundle\CoreBundle\Twig\FilterExtension`
* `Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LiipImageFiltersPass`
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
* `Sylius\Bundle\AddressingBundle\Controller\ProvinceController`
* `Sylius\Bundle\AdminBundle\Controller\NotificationController`
* `Sylius\Bundle\AdminBundle\Twig\NotificationWidgetExtension`
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
* `Sylius\Bundle\ProductBundle\Controller\ProductAttributeController`
* `Sylius\Bundle\UserBundle\Security\UserLogin`
* `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
* `Sylius\Bundle\UserBundle\Security\UserPasswordHasher`
* `Sylius\Bundle\UserBundle\Security\UserPasswordHasherInterface`
* `Sylius\Component\User\Security\Generator\UniquePinGenerator`
* `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController`
* `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
* `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`
* `Sylius\Bundle\AdminBundle\Menu\CustomerShowMenuBuilder`
* `Sylius\Bundle\AdminBundle\Menu\OrderShowMenuBuilder`
* `Sylius\Bundle\AdminBundle\Menu\ProductFormMenuBuilder`
* `Sylius\Bundle\AdminBundle\Menu\ProductUpdateMenuBuilder`
* `Sylius\Bundle\AdminBundle\Menu\ProductVariantFormMenuBuilder`
* `Sylius\Bundle\AdminBundle\Menu\PromotionUpdateMenuBuilder`
* `Sylius\Bundle\AdminBundle\Provider\StatisticsDataProvider`
* `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\CachedRouteNameResolver`
* `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Symfony\Routing\RouteNameResolver`
* `Sylius\Bundle\ApiBundle\ApiPlatform\Factory\MergingExtractorResourceMetadataFactory`
* `Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ProductAttributeCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ProductCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ProductVariantCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\PromotionCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\PromotionCouponCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ProvinceCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ShippingMethodCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\TaxonCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Exception\ZoneCannotBeRemoved`
* `Sylius\Bundle\ApiBundle\Validator\ResourceApiInputDataPropertiesValidator`
* `Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener`
* `Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\CommandDataTransformerInterface`
* `Sylius\Bundle\CoreBundle\Console\Command\Model\PluginInfo`
* `Sylius\Bundle\CoreBundle\Form\Extension\CountryTypeExtension`
* `Sylius\Bundle\CoreBundle\Form\Extension\CustomerTypeExtension`
* `Sylius\Bundle\CoreBundle\Form\Extension\LocaleTypeExtension`
* `Sylius\Bundle\CoreBundle\Form\Type\Grid\Filter\EntitiesFilterType`
* `Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddUserFormSubscriber`
* `Sylius\Bundle\CoreBundle\Twig\StateMachineExtension`
* `Sylius\Component\Core\Grid\Filter\EntitiesFilter`
* `Sylius\Component\Core\Dashboard\DashboardStatistics`
* `Sylius\Component\Core\Dashboard\DashboardStatisticsProvider`
* `Sylius\Component\Core\Dashboard\Interval`
* `Sylius\Component\Core\Dashboard\SalesDataProvider`
* `Sylius\Component\Core\Dashboard\SalesSummary`
* `Sylius\Component\Core\Dashboard\SalesSummaryInterface`
* `Sylius\Bundle\PayumBundle\Action\Paypal\ExpressCheckout\ConvertPaymentAction`
* `Sylius\Bundle\PayumBundle\Controller\PayumController`
* `Sylius\Bundle\PayumBundle\Form\Type\PaypalGatewayConfigurationType`
* `Sylius\Bundle\PayumBundle\Form\Type\StripeGatewayConfigurationType`
* `Sylius\Bundle\UiBundle\ContextProvider\DefaultContextProvider`
* `Sylius\Bundle\UiBundle\Registry\TemplateBlock`
* `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistry`
* `Sylius\Bundle\UiBundle\Renderer\DelegatingTemplateEventRenderer`
* `Sylius\Bundle\UiBundle\Renderer\TwigTemplateBlockRenderer`
* `Sylius\Bundle\UiBundle\Storage\FilterStorageInterface`
* `Sylius\Bundle\UiBundle\Twig\SortByExtension`
* `Sylius\Bundle\UiBundle\Twig\TemplateEventExtension`
* `Sylius\Bundle\UiBundle\Twig\TestFormAttributeExtension`
* `Sylius\Bundle\UiBundle\Twig\TestHtmlAttributeExtension`
* `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface`
* `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScope\ScopeValidatorInterface`
* `Sylius\Component\Core\Promotion\Updater\Rule\TotalOfItemsFromTaxonRuleUpdater`
* `Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManager`
* `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManager`
* `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManager`
* `Sylius\Bundle\ProductBundle\Form\Type\ProductOptionChoiceType`
* `Sylius\Component\Core\Promotion\Updater\Rule\ProductAwareRuleUpdaterInterface`
* `Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker`
* `Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveInactiveCatalogPromotion`
* `Sylius\Bundle\CoreBundle\Provider\SessionProvider`
* `Sylius\Component\Core\SyliusLocaleEvents`
* `Sylius\Bundle\ShopBundle\Twig\OrderTaxesTotalExtension`

1. Renamed

| Old Name                                                                               | New Name                                                                                |
|----------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------|
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResendVerificationEmailHandler`        | `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler`     |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler`   | `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler`   |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler`          | `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler`                  |

1. Moved

| From                                                                               | To                                                                                |
|------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------|
| `Sylius\Bundle\PayumBundle\Validator\GatewayFactoryExistsValidator`                | `Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayFactoryExistsValidator` |
| `Sylius\Bundle\PayumBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator` | `Sylius\Bundle\PaymentBundle\Validator\Constraints\GatewayConfigGroupsGenerator`  |
| `Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker`                     | `Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker`               | 
| `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManager`                        | `Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager`                             |
| `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface`             | `Sylius\Bundle\CoreBundle\Mailer\ShipmentEmailManagerInterface`                   |

#### Services and Aliases

1. Removed

* `sylius.event_subscriber.odm_mapped_super_class`
* `sylius.event_subscriber.odm_repository_class`
* `sylius.grid_driver.doctrine.phpcrodm`
* `sylius.listener.api_postgresql_driver_exception_listener`
* `sylius.security.password_hasher`
* `sylius.security.user_login`
* `Sylius\Bundle\UserBundle\Security\UserLoginInterface`
* `Sylius\Component\User\Security\UserPasswordHasherInterface`
* `sylius.controller.admin.notification`
* `Sylius\Buxndle\AdminBundle\Form\Extension\CatalogPromotionActionTypeExtension`
* `sylius.controller.admin.dashboard.statistics`
* `Sylius\Bundle\AdminBundle\Form\Extension\CatalogPromotionScopeTypeExtension`
* `sylius.admin.menu_builder.customer.show`
* `sylius.admin.menu_builder.order.show`
* `sylius.admin.menu_builder.product_form`
* `sylius.admin.menu_builder.product_variant_form`
* `sylius.admin.menu_builder.promotion.update`
* `Sylius\Bundle\AdminBundle\Provider\StatisticsDataProviderInterface`
* `Sylius\Bundle\ApiBundle\ApiPlatform\ApiResourceConfigurationMerger`
* `api_platform.route_name_resolver.cached`
* `api_platform.route_name_resolver`
* `api_platform.metadata.resource.metadata_factory.yaml`
* `Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\LegacyResourceMetadataMerger`
* `Sylius\Bundle\ApiBundle\ApiPlatform\ResourceMetadataPropertyValueResolver`
* `Sylius\Bundle\ApiBundle\Controller\GetAddressLogEntryCollectionAction`
* `Sylius\Bundle\ApiBundle\Controller\GetOrderAdjustmentsAction`
* `Sylius\Bundle\ApiBundle\Controller\UploadAvatarImageAction`
* `Sylius\Bundle\ApiBundle\Controller\UploadProductImageAction`
* `Sylius\Bundle\ApiBundle\Controller\UploadTaxonImageAction`
* `Sylius\Bundle\ApiBundle\DataPersister\AddressDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\AdminUserDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ChannelDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\CountryDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\CustomerDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\LocaleDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\MessengerDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\PaymentMethodDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ProductAttributeDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ProductDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ProductTaxonDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ProductVariantDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\PromotionCouponDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\PromotionDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ShippingMethodDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\TranslatableDataPersister`
* `Sylius\Bundle\ApiBundle\DataPersister\ZoneDataPersister`
* `Sylius\Bundle\ApiBundle\DataProvider\AccountResetPasswordItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\AdminOrderItemAdjustmentsSubresourceDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\AdminResetPasswordItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ChannelAwareItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ChannelsCollectionDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\CustomerItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\OrderAdjustmentsSubresourceDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\OrderItemAdjustmentsSubresourceDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\OrderItemItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\OrderItemUnitItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\PaymentItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\PaymentMethodsCollectionDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ProductAttributesSubresourceDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ProductItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ShipmentItemDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\ShippingMethodsCollectionDataProvider`
* `Sylius\Bundle\ApiBundle\DataProvider\VerifyCustomerAccountItemDataProvider`
* `Sylius\Bundle\ApiBundle\Filter\Doctrine\PromotionCouponPromotionFilter`
* `Sylius\Bundle\ApiBundle\Filter\PaymentMethodFilter`
* `Sylius\Bundle\ApiBundle\Filter\ShippingMethodFilter`
* `Sylius\Bundle\ApiBundle\QueryHandler\GetAddressLogEntryCollectionHandler`
* `Sylius\Bundle\ApiBundle\Serializer\FlattenExceptionNormalizer`
* `Sylius\Bundle\ApiBundle\SerializerContextBuilder\ReadOperationContextBuilder`
* `Sylius\Bundle\ApiBundle\Validator\Constraints\AccountVerificationTokenEligibilityValidator`
* `Sylius\Bundle\ApiBundle\Validator\ResourceInputDataPropertiesValidatorInterface`
* `Sylius\Bundle\ApiBundle\DataTransformer\ChannelCodeAwareInputCommandDataTransformer`
* `sylius.api.data_transformer.command_aware_input_data_transformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\LocaleCodeAwareInputCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailAwareCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInCustomerEmailIfNotSetAwareCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserIdAwareCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\OrderTokenValueAwareInputCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\ShipmentIdAwareInputCommandDataTransformer`
* `Sylius\Bundle\ApiBundle\DataTransformer\SubresourceIdAwareCommandDataTransformer`
* `api_platform.action.post_item`
* `Sylius\Bundle\CoreBundle\Console\Command\ShowAvailablePluginsCommand`
* `sylius.form.extension.type.country`
* `sylius.form.extension.type.customer`
* `sylius.form.extension.type.locale`
* `sylius.grid_filter.entities`
* `sylius.dashboard.statistics_provider`
* `Sylius\Component\Core\Dashboard\SalesDataProviderInterface`
* `sylius.payum_action.paypal_express_checkout.convert_payment`
* `sylius.controller.payum`
* `sylius.form.type.gateway_configuration.stripe`
* `Sylius\Bundle\UiBundle\Console\Command\DebugTemplateEventCommand`
* `Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface`
* `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockDataCollector`
* `Sylius\Bundle\UiBundle\DataCollector\TemplateBlockRenderingHistory`
* `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateBlockRenderer`
* `Sylius\Bundle\UiBundle\DataCollector\TraceableTemplateEventRenderer`
* `Sylius\Bundle\UiBundle\Registry\TemplateBlockRegistryInterface`
* `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateBlockRenderer`
* `Sylius\Bundle\UiBundle\Renderer\HtmlDebugTemplateEventRenderer`
* `Sylius\Bundle\UiBundle\Renderer\TemplateBlockRendererInterface`
* `Sylius\Bundle\UiBundle\Renderer\TemplateEventRendererInterface`
* `Sylius\Bundle\UiBundle\Storage\FilterStorage`
* `Sylius\Bundle\UiBundle\Twig\LegacySonataBlockExtension`
* `sylius.twig.extension.template_event`
* `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionValidator`
* `Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeValidator`
* `Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface`
* `sylius.promotion_rule_updater.total_of_items_from_taxon`
* `Sylius\Component\Core\Promotion\Updater\Rule\ContainsProductRuleUpdater`
* `Sylius\Bundle\AdminBundle\EmailManager\OrderEmailManagerInterface`
* `sylius.email_manager.shipment`
* `Sylius\Bundle\AdminBundle\EmailManager\ShipmentEmailManagerInterface`
* `Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface`
* `sylius.email_manager.contact`
* `sylius.email_manager.order`
* `Sylius\Bundle\ShopBundle\EmailManager\OrderEmailManagerInterface`
* `sylius.form.type.product_option_choice`
* `Sylius\Bundle\ShopBundle\Calculator\OrderItemsSubtotalCalculatorInterface`
* `sylius.calculator.order_items_subtotal`
* `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveInactiveCatalogPromotionHandler`
* `sylius.http_message_factory`
* `sylius.twig.extension.taxes`

1. Renamed

| Old Name                                                                           | New Name                                                                 |
|------------------------------------------------------------------------------------|--------------------------------------------------------------------------|
| `sylius.twig.extension.form_test_attribute_array`                                  | `sylius_twig_extra.twig.extension.test_form_attribute`                   |
| `sylius.twig.extension.form_test_attribute_name`                                   | `sylius_twig_extra.twig.extension.test_html_attribute`                   |
| `sylius.twig.extension.sort_by`                                                    | `sylius_twig_extra.twig.extension.sort_by`                               |
| `Sylius\Bundle\UiBundle\Twig\RouteExistsExtension`                                 | `sylius_twig_extra.twig.extension.route_exists`                          |
| `sylius.form_registry.payum_gateway_config`                                        | `sylius.form_registry.payment_gateway_config` (moved to `PaymentBundle`) |
| `Sylius\Bundle\PayumBundle\Validator\GatewayFactoryExistsValidator`                | `sylius.validator.gateway_factory_exists`                                |
| `Sylius\Bundle\PayumBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator` | `sylius.validator.groups_generator.gateway_config`                       |

1. Replaced

| Old Name                                                           | New Name                                                         |
|--------------------------------------------------------------------|------------------------------------------------------------------|
| `Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteSubscriber` | `Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteListener` |

1. Definition location changed

  * The `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType` 
    was moved to the `CoreBundle` from `PromotionBundle`.
  * The `Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType` was moved from the `CoreBundle` 
    to the `PromotionBundle`.

#### Class changes

1. Added `Sylius\Component\Order\Context\ResettableCartContextInterface`, which extends `Sylius\Component\Order\Context\CartContextInterface` 
   and `Symfony\Contracts\Service\ResetInterface`.

1. Replaced `sylius/calendar` with `symfony/clock`. All instances of `Sylius\Calendar\Provider\DateTimeProviderInterface` 
   are now replaced by `Symfony\Component\Clock\ClockInterface`.

  Affected classes:
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncer`
    - `Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account\RequestResetPasswordEmailHandler`
    - `Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger`
    - `Sylius\Bundle\CoreBundle\PriceHistory\Remover\ChannelPricingLogEntriesRemover`
    - `Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssigner`
    - `Sylius\Bundle\PromotionBundle\Criteria\DateRange`
    - `Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicator`
    - `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestResetPasswordTokenHandler`
    - `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyCustomerAccountHandler`
    - `Sylius\Component\Taxation\Checker\TaxRateDateEligibilityChecker`

1. The `\Serializable` interface has been removed from `Sylius\Component\User\Model\UserInterface`.

#### New and Updated Repository Classes

##### New Repositories
- **Addressing**:
    - `Sylius\Bundle\AddressingBundle\Doctrine\ORM\AddressRepository`
    - `Sylius\Bundle\AddressingBundle\Doctrine\ORM\CountryRepository`
    - `Sylius\Bundle\AddressingBundle\Doctrine\ORM\ProvinceRepository`
    - `Sylius\Bundle\AddressingBundle\Doctrine\ORM\ZoneMemberRepository`

- **Attribute**:
    - `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeRepository`
    - `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeTranslationRepository`
    - `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeValueRepository`

- **Product**:
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationTypeTranslationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAttributeTranslationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionTranslationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionValueRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionValueTranslationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductTranslationRepository`
    - `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantTranslationRepository`

- **Currency**:
    - `Sylius\Bundle\CurrencyBundle\Doctrine\ORM\CurrencyRepository`

##### Repository Inheritance Changes

- **Addressing**:
    - `Sylius\Bundle\CoreBundle\Doctrine\ORM\AddressRepository` now extends `Sylius\Bundle\AddressingBundle\Doctrine\ORM\AddressRepository`.
    - `Sylius\Component\Core\Repository\AddressRepositoryInterface` now implements `Sylius\Component\Addressing\Repository\AddressRepositoryInterface`.

- **Attribute**:
    - `Sylius\Bundle\CoreBundle\Doctrine\ORM\AttributeRepository` now extends `Sylius\Bundle\AttributeBundle\Doctrine\ORM\AttributeRepository`.
    - `Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface` now implements `Sylius\Component\Attribute\Repository\AttributeValueRepositoryInterface`.

- **Product**:
    - `Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductAssociationRepository` now extends `Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductAssociationRepository`.

#### User Model Updates

- Removed fields and corresponding methods for:
    - `locked`
    - `expiresAt`
    - `credentialsExpireAt`
- These changes affect the ShopUser and AdminUser models, and any custom user type extending the `Sylius\Component\User\Model\User` model, 
  as well as the relevant columns in the database tables.

#### Service Aliases

* Aliases introduced in Sylius 1.14 have now become the primary service IDs in Sylius 2.0. The old service IDs have been
  removed, and all references must be updated accordingly:

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
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\RequestShopUserVerificationHandler`                                 | `sylius_api.command_handler.account.request_shop_user_verification`                                 |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\ResetPasswordHandler`                                               | `sylius_api.command_handler.account.reset_password`                                                 |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler`                                | `sylius_api.command_handler.account.send_account_registration_email`                                |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendShopUserVerificationEmailHandler`                               | `sylius_api.command_handler.account.send_shop_user_verification_email`                              |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendOrderConfirmationHandler`                                      | `sylius_api.command_handler.checkout.send_order_confirmation`                                       |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler`                                      | `sylius_api.command_handler.account.send_reset_password_email`                                      |
| `Sylius\Bundle\ApiBundle\CommandHandler\Checkout\SendShipmentConfirmationEmailHandler`                              | `sylius_api.command_handler.checkout.send_shipment_confirmation_email`                              |
| `Sylius\Bundle\ApiBundle\CommandHandler\Account\VerifyShopUserHandler`                                              | `sylius_api.command_handler.account.verify_shop_user`                                               |
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
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\AddressDenormalizer`                                               | `sylius_api.denormalizer.address`                                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer`                                      | `sylius_api.denormalizer.command_arguments`                                                         |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer`                                               | `sylius_api.denormalizer.command`                                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductNormalizer`                                                   | `sylius_api.normalizer.product`                                                                     |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductAttributeValueDenormalizer`                                 | `sylius_api.denormalizer.product_attribute_value`                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductDenormalizer`                                               | `sylius_api.denormalizer.product`                                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductAttributeValueNormalizer`                                     | `sylius_api.normalizer.product_attribute_value`                                                     |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer`                                                     | `sylius_api.normalizer.image`                                                                       |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\CommandNormalizer`                                                   | `sylius_api.normalizer.command`                                                                     |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer`                                            | `sylius_api.normalizer.product_variant`                                                             |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ShippingMethodNormalizer`                                            | `sylius_api.normalizer.shipping_method`                                                             |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ZoneDenormalizer`                                                  | `sylius_api.denormalizer.zone`                                                                      |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableDenormalizer`                                          | `sylius_api.denormalizer.translatable`                                                              |
| `date_time_normalizer`                                                                                              | `sylius_api.normalizer.date_time`                                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelPriceHistoryConfigDenormalizer`                             | `sylius_api.denormalizer.channel_price_history_config`                                              |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelDenormalizer`                                               | `sylius_api.denormalizer.channel`                                                                   |
| `sylius.api.denormalizer.numeric_to_string.tax_rate`                                                                | `sylius_api.denormalizer.numeric_to_string.tax_rate`                                                |
| `sylius.api.denormalizer.numeric_to_string.exchange_rate`                                                           | `sylius_api.denormalizer.numeric_to_string.exchange_rate`                                           |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CustomerDenormalizer`                                              | `sylius_api.denormalizer.customer`                                                                  |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableLocaleKeyDenormalizer`                                 | `sylius_api.denormalizer.translatable_locale_key`                                                   |
| `Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductVariantChannelPricingsChannelCodeKeyDenormalizer`           | `sylius_api.denormalizer.product_variant_channel_pricings_channel_code_key`                         |
| `Sylius\Bundle\ApiBundle\Serializer\Normalizer\DoctrineCollectionValuesNormalizer`                                  | `sylius_api.normalizer.doctrine_collection_values`                                                  |
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
| `sylius.availability_checker`                                                                                       | `sylius.checker.inventory.availability`                                                             |
| `sylius.availability_checker.default`                                                                               | `sylius.checker.inventory.availability`                                                             |
| **LocaleBundle**                                                                                                    |                                                                                                     |
| `Sylius\Bundle\LocaleBundle\Context\RequestHeaderBasedLocaleContext`                                                | `sylius.context.locale.request_header_based`                                                        |
| `sylius.locale_collection_provider`                                                                                 | `sylius.provider.locale_collection`                                                                 |
| `sylius.locale_collection_provider.cahced`                                                                          | `sylius.provider.locale_collection.cached`                                                          |
| `sylius.locale_provider`                                                                                            | `sylius.provider.locale`                                                                            |
| `sylius.locale_converter`                                                                                           | `sylius.converter.locale`                                                                           |
| `Sylius\Bundle\LocaleBundle\Doctrine\EventListener\LocaleModificationListener`                                      | `sylius.doctrine.listener.locale_modification`                                                      |
| **MoneyBundle**                                                                                                     |                                                                                                     |
| `sylius.twig.extension.convert_amount`                                                                              | `sylius.twig.extension.convert_money`                                                               |
| `sylius.twig.extension.money`                                                                                       | `sylius.twig.extension.format_money`                                                                |
| `sylius.money_formatter`                                                                                            | `sylius.formatter.money`                                                                            |
| **OrderBundle**                                                                                                     |                                                                                                     |
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
| **ProductBundle**                                                                                                   |                                                                                                     |
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

The old service IDs are no longer available in Sylius 2.0. Please ensure your configurations and service references use
the new service IDs.

* The following services had new aliases added in Sylius 1.14. In Sylius 2.0, these aliases have become the primary
  service IDs, and the old service IDs remain as aliases:

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
| **ProductBundle**                                                                                                        |                                                                                          |
| `Sylius\Component\Product\Resolver\ProductVariantResolverInterface`                                                      | `sylius.resolver.product_variant`                                                        |
| **PromotionBundle**                                                                                                      |                                                                                          |
| `Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface`                                      | `sylius.provider.eligible_catalog_promotions`                                            |
| **TaxonomyBundle**                                                                                                       |                                                                                          |
| `Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface`                                                   | `sylius.custom_repository.tree.taxon`                                                    |

### Architecture changes

* The `Normalizer` and `Denormalizer` in `ApiBundle` have been reorganized into subdirectories, moving from `Sylius\Bundle\ApiBundle\Serializer`
  to `Sylius\Bundle\ApiBundle\Serializer\Normalizer` and `Sylius\Bundle\ApiBundle\Serializer\Denormalizer`.

* The `SerializerContextBuilder` classes in `ApiBundle` have been moved to the `Sylius\Bundle\ApiBundle\Serializer\ContextBuilder` subdirectory.

* The `Filter`, `QueryCollectionExtension`, and `QueryItemExtension` classes in `ApiBundle` have been reorganized into separate sections (`admin`, `shop`, `common`)
  based on their usage context, and grouped by resources.

* The `Message` directory has been renamed to `Command`. Following this change, `MessageHandler` has been renamed to `CommandHandler`, and `MessageDispatcher` has been renamed to `CommandDispatcher`.

* `AdminBundle` now contains base form types for every resource. 
Use these as an extension point for admin customizations instead the ones from `CoreBundle` or other minor bundles.
Similarly, use form types from `ShopBundle` for the Shop context.

### Payment method gateways

* Stripe and Paypal Express Checkout gateways have been removed.
  The only remaining by default gateway in core is `offline`. Use sylius plugins for the gateways of your choosing.

### Theming

* Channel's `themeName` form field existence is made optional and depends on `ShopBundle` presence.
* The `Sylius\Bundle\CoreBundle\Theme\ChannelBasedThemeContext` has been moved to 
  the `Sylius\Bundle\ShopBundle\Theme\ChannelBasedThemeContext`.

### Frontend

* Unused, legacy node packages have been removed, while the required ones have been updated to newer versions. 
  To ensure a smooth transition, it is recommended to delete the `node_modules` directory and reinstall the packages.
* The recommended Node.js versions are 20 or 22, as support for version 18 has been dropped.
* `use_webpack` option was removed from the `sylius_ui` configuration, and the Webpack has become the only module
  bundler provided by Sylius.
* `use_webpack` twig global variable was removed. Webpack is always used now, and there is no need to check for it.
* Image sizes have been simplified and standardized for both the Admin and Shop Bundle.
* Some Twig extension services have been moved from the UiBundle to the new Twig Extra package

#### KNP Menu

* Aliases for the `knp_menu.menu_builder` tags introduced in Sylius 1.14 are now the only valid menu builder tags in
  Sylius 2.0:

| Old Alias             | New Alias             |
|-----------------------|-----------------------|
| **AdminBundle**       |                       |
| `sylius.admin.main`   | `sylius_admin.main`   |
| **ShopBundle**        |                       |
| `sylius.shop.account` | `sylius_shop.account` |

#### Transition from SemanticUI to Bootstrap

- All CSS classes of SemanticUI have been replaced with Bootstrap classes.
- JavaScript components relying on Semantic UI have been rewritten to utilize Bootstrap's JavaScript plugins.
- Customized CSS has been replaced by Bootstrap's utility classes.

#### Removal of jQuery

Most of the existing JavaScript has been replaced by SymfonyUX with Stimulus, which includes live components.
This change led to the removal of jQuery and a significant reduction of custom JavaScript in the project. 
Check out the documentation for more information [here](https://ux.symfony.com/).

#### Abandoning partial routes

All partial routes rendered in templates have been removed and replaced by rendering Twig components.

#### Sylius Twig Hooks

Twig Hooks are a robust and powerful alternative to the Sonata Block Events and the old Sylius Template Events systems.

##### Removal of Sonata Blocks

Sonata Blocks have been fully removed as they were not actively maintained/supported for a long time.

##### Evolving Sylius Template Events to Twig Hooks

Sylius Twig Hooks is a new generation of template customization and extension, providing:

- Built-in support for Twig templates, Twig Components, and Symfony Live Components.
- Adjustability and autoprefixing hooks.
- A configurable and easily manageable system for hookables.
- A priority mechanism to control rendering order.
- Simple enable/disable options for each hook.

1. Key Improvements in Sylius Twig Hooks

**Improved Structure**

- **Hooks**

Previously, all template events were configured in a single, monolithic `events.yaml` file, making it difficult to navigate and maintain:

```
/app
    /config
        /app
            /events.yaml
```

With Twig Hooks, the configuration has been reorganized into smaller, more manageable files. Each file corresponds 
to a specific part of the application, enhancing clarity and maintainability:

```
/app
    /config
        /app
            /twig_hooks
                /product
                    /create.yaml
                    /update.yaml
                    /index.yaml
                    /show.yaml
```

- **Templates**

The structure of the template directories has also been improved. Previously, templates were organized in a less intuitive way, 
with some structures inconsistent with the template event definitions:

```
/resources
    /views
        /AdminUser
        /Crud
        /Product
            /Form
            /_avatarImage.html.twig
            /_form.html.twig
```

Now, the templates are organized in a more consistent manner, with all templates grouped by resource and aligned 
with hook naming conventions:

```
/templates
    /product
        /form
            /sections
                /translations
                    description.html.twig
                    meta_description.html.twig
                    meta_keywords.html.twig
                    name.html.twig
                    short_description.html.twig
                    slug.html.twig
                /translations.html.twig
        /show
    /shared
    /another_resource
```

1. Detailed Comparison: Old vs. New configurations

**Old configuration (Template Events)**

The previous approach grouped all event blocks within `events.yaml`, which led to a cluttered and hard-to-manage configuration:

```
sylius_ui:
    events:
        sylius.admin.index:
            blocks:
                before_header_legacy:
                    template: "@SyliusAdmin/Crud/Block/_legacySonataEvent.html.twig"
                    priority: 25
                    context:
                        postfix: index.before_header
                header:
                    template: "@SyliusAdmin/Crud/Index/_header.html.twig"
                    priority: 20
                after_header_legacy:
                    template: "@SyliusAdmin/Crud/Block/_legacySonataEvent.html.twig"
                    priority: 15
                    context:
                        postfix: index.after_header
                content:
                    template: "@SyliusAdmin/Crud/Index/_content.html.twig"
                    priority: 10
                after_content:
                    template: "@SyliusAdmin/Crud/Block/_legacySonataEvent.html.twig"
                    priority: 5
                    context:
                        postfix: index.after_content
```

**New configuration (Twig Hooks)**

The new system organizes hooks by specific parts of the application, simplifying customization and improving readability:

```
sylius_twig_hooks:
    hooks:
        'sylius_admin.common.index':
            sidebar:
                template: '@SyliusAdmin/shared/crud/common/sidebar.html.twig'
                priority: 200
            navbar:
                template: '@SyliusAdmin/shared/crud/common/navbar.html.twig'
                priority: 100
            content:
                template: '@SyliusAdmin/shared/crud/common/content.html.twig'
                priority: 0

        'sylius_admin.common.index.content':
            flashes:
                template: '@SyliusAdmin/shared/crud/common/content/flashes.html.twig'
                priority: 300
            header:
                template: '@SyliusAdmin/shared/crud/common/content/header.html.twig'
                priority: 200
            grid:
                template: '@SyliusAdmin/shared/crud/index/content/grid.html.twig'
                priority: 100
            footer:
                template: '@SyliusAdmin/shared/crud/common/content/footer.html.twig'
                priority: -100

        'sylius_admin.common.index.content.header':
            breadcrumbs:
                template: '@SyliusAdmin/shared/crud/index/content/header/breadcrumbs.html.twig'
                priority: 100
            title_block:
                template: '@SyliusAdmin/shared/crud/common/content/header/title_block.html.twig'
                priority: 0

        'sylius_admin.common.index.content.header.title_block':
            title:
                template: '@SyliusAdmin/shared/crud/common/content/header/title_block/title.html.twig'
                priority: 100
            actions:
                template: '@SyliusAdmin/shared/crud/common/content/header/title_block/actions.html.twig'
                priority: 0

        'sylius_admin.common.index.content.grid':
            filters:
                template: '@SyliusAdmin/shared/crud/index/content/grid/filters.html.twig'
                priority: 200
            data_table:
                template: '@SyliusAdmin/shared/crud/index/content/grid/data_table.html.twig'
                priority: 100
            no_data_block:
                template: '@SyliusAdmin/shared/crud/index/content/grid/no_results.html.twig'
                priority: 0

        'sylius_admin.common.index.content.grid.no_results':
            image:
                template: '@SyliusAdmin/shared/crud/index/content/grid/no_results/image.html.twig'
                priority: 300
            title:
                template: '@SyliusAdmin/shared/crud/index/content/grid/no_results/title.html.twig'
                priority: 200
            subtitle:
                template: '@SyliusAdmin/shared/crud/index/content/grid/no_results/subtitle.html.twig'
                priority: 100
            action:
                template: '@SyliusAdmin/shared/crud/index/content/grid/no_results/action.html.twig'
                priority: 0
```

Twig Hooks cover both the admin and shop areas comprehensively, ensuring consistency across the entire application. 
AdminBundle hooks start with the `sylius_admin` prefix, while ShopBundle hooks start with the `sylius_shop` prefix.

For more information visit the [Sylius Stack](https://github.com/Sylius/Stack).

### Testing Suite

* The `sylius.behat.api_security` has been replaced by `sylius.behat.api_admin_security` and `sylius.behat.api_shop_security` services.
* We removed `Psalm`, the `PHPStan` is now the only static analysis tool used in the project.
