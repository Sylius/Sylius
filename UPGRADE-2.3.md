# UPGRADE FROM `2.2` TO `2.3`

## Security

1. The `sylius.user_checker.enabled` service (`Sylius\Component\User\Security\Checker\EnabledUserChecker`) is **no longer tagged** for the `shop` firewall.

   It has been replaced by two new checkers on the `shop` firewall:

   - `sylius.user_checker.verification_aware_enabled` (`Sylius\Bundle\CoreBundle\Security\Checker\VerificationAwareEnabledUserChecker`, priority 100) — behaves like `EnabledUserChecker` except it allows users with a pending email verification (channel requires verification and `verifiedAt` is `null`) to pass the pre-authentication check, so the password is validated and the "not verified" message is shown.
   - `sylius.user_checker.email_verification` (`Sylius\Bundle\CoreBundle\Security\Checker\EmailVerificationUserChecker`, priority 50) — throws a `sylius.user.email_not_verified` error after successful password validation when the user has not verified their email on a channel that requires verification.

   If you have decorated or replaced `sylius.user_checker.enabled` specifically for the `shop` firewall, migrate your customization to `sylius.user_checker.verification_aware_enabled` instead.

## Promotions

1. A new `trackUsage` field has been added to promotions and promotion coupons. ([#18966](https://github.com/Sylius/Sylius/pull/18966))

   When `trackUsage` is disabled, the usage counter is neither incremented nor decremented when an order is placed or cancelled.
   This allows promotions and coupons to be used without affecting their usage statistics.

   The following methods have been added to their respective interfaces:

   - `Sylius\Component\Promotion\Model\PromotionInterface`:

     ```php
     public function isTrackUsage(): bool;
     public function setTrackUsage(bool $trackUsage): void;
     ```

   - `Sylius\Component\Promotion\Model\PromotionCouponInterface`:

     ```php
     public function isTrackUsage(): bool;
     public function setTrackUsage(bool $trackUsage): void;
     ```

   If you have custom classes implementing these interfaces, you must add these methods.
   The default value is `true`, preserving the previous behaviour.

2. The `isTrackUsage(): bool` method has been added to `Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface`

   If you have a custom class implementing this interface, you must add this method.

3. The behaviour of the following eligibility checkers has changed — they now return `true` (eligible) when `trackUsage` is disabled, regardless of how many times the promotion or coupon has already been used

   - `Sylius\Component\Promotion\Checker\Eligibility\PromotionUsageLimitEligibilityChecker`
   - `Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponUsageLimitEligibilityChecker`

   This is consistent with the existing behaviour of `PromotionCouponPerCustomerUsageLimitEligibilityChecker` and prevents non-zero legacy `used` counters from blocking customers after `trackUsage` is turned off.

4. The following usage modifiers now skip promotions and coupons that have `trackUsage` disabled

   - `Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier`
   - `Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier`

   If you have decorated or extended these classes, verify that your implementation also respects the `isTrackUsage()` flag.

5. The `countByCustomerAndCoupon()` method on `Sylius\Component\Core\Repository\OrderRepositoryInterface` and its implementation in `Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository` has been deprecated and will be removed in Sylius 3.0.

   Use `countByCustomerAndCouponSince()` instead, passing `null` as the `$since` argument to replicate the previous behaviour:

   ```php
   // Before
   $repository->countByCustomerAndCoupon($customer, $coupon);

   // After
   $repository->countByCustomerAndCouponSince($customer, $coupon, null);
   ```

## Messenger

1. A new `Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail` message has been introduced. ([#19002](https://github.com/Sylius/Sylius/pull/19002))

   If you use an async transport, add the routing configuration:

   ```yaml
   framework:
       messenger:
           routing:
               'Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail': your_async_transport
   ```

## Shop

1. When an unverified account tries to log in with valid credentials on a channel that requires account verification, the login page now shows a one-click "resend verification email" action instead of a separate request form.

   - A new `resend_verification_email` hookable (priority `-100`) is added to the login page container hook `sylius_shop.account.login.content.login_container`. It renders a `POST` form (CSRF-protected) that submits to `sylius_shop_resend_verification_email`.
   - The `sylius_shop_resend_verification_email` route is now `POST`-only and handled by `ResendVerificationEmailController::resendAction()`. It re-sends the verification email to the last authenticated email (read from the session), then redirects back to the login page. There is no standalone resend page or form.

## Configuration

1. The default value of `sylius_core.order_by_identifier` has been changed from `true` to `false`. ([#18956](https://github.com/Sylius/Sylius/pull/18956))

   The `OrderByIdentifierSqlWalker` is no longer enabled by default.
   If your application relies on ordering by identifier, enable it explicitly in your configuration:

   ```yaml
   sylius_core:
       order_by_identifier: true
   ```

2. Configuration changes related to the broadened Doctrine support (see the *Dependencies* section). 
   These only matter once you opt into **DoctrineBundle 3** / **DBAL 4**:

   - The `auto_generate_proxy_classes: "%kernel.debug%"` option was removed from Sylius' Doctrine configuration 
     (it no longer exists in DoctrineBundle 3, and since PHP 8.4 Doctrine uses native lazy objects). If you are still 
     on DoctrineBundle 2 and rely on this behavior, set it explicitly in your own application configuration.

   - The ORM metadata/query/result cache configuration was switched from wrapped `Doctrine\Common\Cache` services 
     to **PSR-6 cache pools** (`type: pool`). This change lives in the application configuration (`config/packages/{prod,test}/doctrine.yaml`), 
     which is **not** updated automatically on existing installations, update those files in your project accordingly:

     ```diff
     doctrine:
         orm:
             entity_managers:
                 default:
                     metadata_cache_driver:
     -                    type: service
     -                    id: doctrine.system_cache_provider
     +                    type: pool
     +                    pool: doctrine.system_cache_pool
     ```

     As part of this switch, the `doctrine.result_cache_provider` and `doctrine.system_cache_provider` services 
     (defined in Sylius' application configuration, wrapping the PSR-6 pools via `Doctrine\Common\Cache\Psr6\DoctrineProvider`) 
     were **removed**. If you referenced them by id, use the cache pools directly (`doctrine.system_cache_pool`, 
     `doctrine.result_cache_pool`) with `type: pool` as shown above.

   - On **PostgreSQL**, Sylius now forces the `SEQUENCE` identity generation strategy 
     (`identity_generation_preferences` for `PostgreSQLPlatform`) to keep the database schema backward compatible 
     with existing installations.

3. The `_sylius: redirect:` block has been removed from the `sylius_shop_order_pay` route definition.

   Previously, `PayumPayResponseProvider` read the after-pay redirect route at runtime from the routing metadata:

   ```yaml
   # ShopBundle/Resources/config/routing/order.yml (removed)
   sylius_shop_order_pay:
       defaults:
           _sylius:
               redirect:
                   route: sylius_shop_order_after_pay
   ```

   The redirect route is now resolved from the `sylius_shop` bundle configuration at compile time.
   If you customized the after-pay redirect route for Payum, use the dedicated Payum configuration keys instead:

   ```yaml
   sylius_shop:
       order_pay:
           payum_after_pay_route: sylius_shop_order_after_pay
           payum_after_pay_route_parameters: []
   ```

   > **Note:** The `after_pay_route` and `after_pay_route_parameters` keys are reserved for the Payment Request flow
   > and their default parameters include Payment Request-specific values (e.g. `hash: paymentRequest.getHash()`).
   > Do not use them to configure the Payum after-pay redirect.

4. The `RequestConfiguration` type has been replaced by `Request` in all payment processing interfaces and their implementations.

   If you implemented or decorated any of the following interfaces, update your method signatures accordingly:

   - `Sylius\Bundle\CoreBundle\OrderPay\Provider\PayResponseProviderInterface`:
     ```diff
     -public function getResponse(RequestConfiguration $requestConfiguration, OrderInterface $order): Response;
     -public function supports(RequestConfiguration $requestConfiguration, OrderInterface $order): bool;
     +public function getResponse(Request $request, OrderInterface $order): Response;
     +public function supports(Request $request, OrderInterface $order): bool;
     ```

   - `Sylius\Bundle\CoreBundle\OrderPay\Provider\AfterPayResponseProviderInterface`:
     ```diff
     -public function getResponse(RequestConfiguration $requestConfiguration): Response;
     -public function supports(RequestConfiguration $requestConfiguration): bool;
     +public function getResponse(Request $request): Response;
     +public function supports(Request $request): bool;
     ```

   - `Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface`:
     ```diff
     -public function supports(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): bool;
     -public function getResponse(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): Response;
     +public function supports(Request $request, PaymentRequestInterface $paymentRequest): bool;
     +public function getResponse(Request $request, PaymentRequestInterface $paymentRequest): Response;
     ```

   - `Sylius\Bundle\PaymentBundle\Processor\HttpResponseProcessorInterface`:
     ```diff
     -public function process(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): ?Response;
     +public function process(Request $request, PaymentRequestInterface $paymentRequest): ?Response;
     ```

   - `Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandlerInterface`:
     ```diff
     -public function handle(RequestConfiguration $requestConfiguration, string $state): void;
     +public function handle(Request $request, string $state): void;
     ```

5. `MetadataInterface` and `RequestConfigurationFactoryInterface` have been removed from the constructors of `OrderPayController` and `PaymentRequestPayAction`.

   If you extended or decorated either of these classes, update their constructors:

   - `Sylius\Bundle\CoreBundle\OrderPay\Controller\OrderPayController`:
     ```diff
     public function __construct(
         private OrderRepositoryInterface $orderRepository,
     -    private MetadataInterface $orderMetadata,
     -    private RequestConfigurationFactoryInterface $requestConfigurationFactory,
         private iterable $payResponseProviders,
         private iterable $afterPayResponseProviders,
     ) {}
     ```

   - `Sylius\Bundle\CoreBundle\OrderPay\Action\PaymentRequestPayAction`:
     ```diff
     public function __construct(
     -    private MetadataInterface $paymentRequestMetadata,
     -    private RequestConfigurationFactoryInterface $requestConfigurationFactory,
         private PaymentRequestRepositoryInterface $paymentRequestRepository,
         private HttpResponseProcessorInterface $httpResponseProcessor,
         private UrlProviderInterface $afterPayUrlProvider,
     ) {}
     ```
6. The payment encryption (`sylius_payment.encryption`) got two new options to harden decryption of gateway configs
   and payment requests. Both are opt-in and keep the previous behavior by default.

   - `strict_mode` (default `false`): when enabled, `Sylius\Component\Payment\Encryption\Encrypter::decrypt()` throws
     an `EncryptionException` for data that is not encrypted, instead of silently returning it unchanged.
     Additionally, `GatewayConfigEncrypter` and `PaymentRequestEncrypter` throw an `EncryptionException` when the
     gateway config, payload or response data is only partially encrypted. By default (non-strict) they keep the
     previous lenient behavior: partially encrypted data is still decrypted based on its first element.

     ```yaml
     sylius_payment:
         encryption:
             strict_mode: true
     ```

   - `allowed_classes` (default `true`): restricts which PHP classes may be instantiated while decrypting
     (the `allowed_classes` option of `unserialize()`). Use `true` to allow all classes (previous behavior),
     `false` to allow none, or a list of class-strings to allow only specific ones. If you store objects in a
     gateway config or payment request payload (for example a payment plugin deserializing SDK objects such as
     `Stripe\PaymentIntent`), add those classes to the list when narrowing it down.

     ```yaml
     sylius_payment:
         encryption:
             allowed_classes:
                 - 'Stripe\PaymentIntent'
     ```

   The related encrypters gained a matching constructor argument, defaulting to the previous behavior:

   ```diff
   -public function __construct(private readonly string $encryptionKeyPath)
   +public function __construct(private readonly string $encryptionKeyPath, private readonly bool $strictDecryption = false)
   ```

   ```diff
   -public function __construct(private EncrypterInterface $encrypter)
   +public function __construct(private EncrypterInterface $encrypter, private array|bool $allowedClasses = true, private bool $strictMode = false)
   ```

   Affected classes: `Sylius\Component\Payment\Encryption\Encrypter` (`$strictDecryption`),
   `Sylius\Component\Payment\Encryption\GatewayConfigEncrypter` and
   `Sylius\Component\Payment\Encryption\PaymentRequestEncrypter` (`$allowedClasses` and `$strictMode`).

### Grid providers are now configurable

As part of the ongoing modernization of the Grid component, Sylius now provides grid definitions in both **YAML** and **PHP**.

Moving grid configuration to PHP provides several benefits:

* Better IDE support, including autocompletion and static analysis.
* Improved maintainability for complex grid definitions.

PHP grid definitions are the recommended approach going forward and represent the direction of Sylius 3.0. To support a
smooth migration, Sylius continues to support legacy YAML-based grids and now allows you to choose which format is used 
for each grid.

### Migration strategy

During the migration period, grid definitions can be loaded from either:

* **YAML** (legacy format)
* **PHP** (recommended format)

You can configure the format globally:

```yaml
sylius_core:
    grid:
        use_legacy_config: true # Use YAML grid definitions globally (default: false)
```

Or override the format for individual grids:

```yaml
sylius_core:
    grid:
        grids:
            sylius_admin_product_variant:
                use_legacy_config: false # Use PHP configuration for this grid
```

This makes it possible to migrate grids incrementally rather than converting your entire application at once.

> **Important**
>
> A grid can only be loaded from a single source. YAML and PHP definitions for the same grid cannot be merged. When migrating a grid to PHP, you must recreate the complete grid definition in PHP, including any vendor configuration you want to preserve.

### Migration tooling

To simplify the conversion process, you can use the community-maintained Grid configuration converter:

* [Grid Config Converter](https://github.com/mamazu/grid-config-converter?utm_source=chatgpt.com)

The converter can help bootstrap the migration from YAML to PHP and reduce the amount of manual work required.

### Learn more

For a complete overview of the Grid component, see the [Grid documentation](https://stack.sylius.com/grid/index).

## Installer

1. The `sylius:install:setup` command now sets up a **country** and a **default zone** during installation, and
   optionally assigns the created zone as the default channel tax zone.

   Three new setup classes have been introduced:
   - `Sylius\Bundle\CoreBundle\Installer\Setup\CountrySetup` implementing `CountrySetupInterface`
   - `Sylius\Bundle\CoreBundle\Installer\Setup\ZoneSetup` implementing `ZoneSetupInterface`
   - `Sylius\Bundle\CoreBundle\Installer\Setup\ChannelDefaultTaxZoneSetup` implementing `ChannelDefaultTaxZoneSetupInterface`

   All three are registered as services and injected into `SetupCommand` as **optional** constructor arguments. If
   you have decorated or replaced `SetupCommand`, not passing them is deprecated and will be prohibited in
   Sylius 3.0:

   ```diff
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly CommandDirectoryChecker $commandDirectoryChecker,
        protected readonly CurrencySetupInterface $currencySetup,
        protected readonly LocaleSetupInterface $localeSetup,
        protected readonly ChannelSetupInterface $channelSetup,
        protected readonly FactoryInterface $adminUserFactory,
        protected readonly UserRepositoryInterface $adminUserRepository,
        protected readonly ValidatorInterface $validator,
   +    protected readonly ?CountrySetupInterface $countrySetup = null,
   +    protected readonly ?ZoneSetupInterface $zoneSetup = null,
   +    protected readonly ?ChannelDefaultTaxZoneSetupInterface $channelDefaultTaxZoneSetup = null,
    )
   ```

2. The `ChannelSetupInterface::setup()` method signature has been extended with an optional argument to receive the
   newly created country:

   ```diff
    public function setup(
        LocaleInterface $locale,
        CurrencyInterface $currency,
   +    ?CountryInterface $country = null,
    ): void;
   ```

   If you have a custom implementation of `ChannelSetupInterface`, update its `setup()` signature accordingly.

## Dependencies

1. The `behat/transliterator` package has been **deprecated** and will be removed in Sylius 3.0.

    Slug generation now **primarily** uses `symfony/string` (`Symfony\Component\String\Slugger\SluggerInterface`).
    When no `$slugger` is injected, the system falls back to `Behat\Transliterator\Transliterator`, but this fallback behavior is deprecated and will be removed in Sylius 3.0.

   The following classes have been updated — if you have extended or decorated them, update your constructor accordingly:

   - `Sylius\Component\Product\Generator\SlugGenerator`:

     ```diff
     -public function __construct()
     +public function __construct(private ?SluggerInterface $slugger)
     ```

     > **Deprecated:** Passing `null` as `$slugger` (or omitting it) is deprecated since Sylius 2.3.
     > When `null` is passed, the generator falls back to `Behat\Transliterator\Transliterator`.
     > Both the nullable argument and the `behat/transliterator` fallback will be removed in Sylius 3.0.

   - `Sylius\Component\Taxonomy\Generator\TaxonSlugGenerator`:

     ```diff
     -public function __construct()
     +public function __construct(private ?SluggerInterface $slugger)
     ```

     > **Deprecated:** Same as above.

   - `Sylius\Bundle\AdminBundle\Generator\TaxonSlugGenerator`:

     ```diff
      public function __construct(
          private BaseTaxonSlugGeneratorInterface $slugGenerator,
     +    private ?SluggerInterface $slugger,
      )
     ```

     > **Deprecated:** Same as above.

2. The `StringInflector::nameToSlug()` method has been **deprecated** and will be removed in Sylius 3.0.

3. The minimum required **PHP version** has been raised from `^8.2` to `^8.3`.

   Ensure your environment runs PHP 8.3 or higher before upgrading.

4. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.
   
   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.

5. The `symfony/proxy-manager-bridge` and `friendsofphp/proxy-manager-lts` packages have been removed.

   They are no longer needed, lazy services now rely on PHP's native lazy proxies provided by
   `symfony/var-exporter` (the default since Symfony 6.4). No change is required in your application.

   If your application depends on these packages directly, require them explicitly in your `composer.json`.

6. The supported Doctrine version ranges have been **broadened** to allow the newer stack
   (`doctrine/doctrine-bundle` `^2.13 || ^3.0`, `doctrine/dbal` `^3.9 || ^4.0`,
   `doctrine/persistence` `^3.3 || ^4.0`, `doctrine/data-fixtures` `^1.7 || ^2.2`).

   DBAL 4 removes the built-in `object` and `array` column types. Since Sylius maps two fields 
   as `type="object"` (`PaymentSecurityToken.details` and `PaymentRequest.payload`), it registers
   a custom `Sylius\Bundle\PaymentBundle\Doctrine\DBAL\Type\ObjectType` to keep them working.

## Validation

1. Passing an array of options to configure a Sylius validation constraint is **deprecated** since Sylius 2.3
   and will be removed in Sylius 3.0. Use named arguments instead.

   All Sylius validation constraints now declare explicit constructors with named arguments
   (marked with `#[HasNamedArguments]`). The legacy array syntax keeps working, it only triggers deprecation.

   Configuring constraints via **XML / YAML / PHP attributes is not affected** and requires no changes; the validator
   loaders pass the options as named arguments automatically. Only **direct instantiation in PHP** should be migrated:

   ```diff
   -new ProvinceAddressConstraint(['message' => 'My custom message'])
   +new ProvinceAddressConstraint(message: 'My custom message')
   ```

2. Several constraint message options have been renamed to follow the consistent `*Message` convention. The old option
   names and public properties are **deprecated** since Sylius 2.3 and will be removed in Sylius 3.0. Both keep working
   and stay in sync in the meantime; switch to the new `*Message` name:

   | Constraint | Old | New |
   |------------|-----|-----|
   | `ApiBundle\...\ChosenPaymentMethodEligibility` | `notAvailable`, `notExist`, `paymentNotFound` | `notAvailableMessage`, `notExistMessage`, `paymentNotFoundMessage` |
   | `ApiBundle\...\ChosenPaymentRequestActionEligibility` | `notAvailable`, `notExist` | `notAvailableMessage`, `notExistMessage` |
   | `ApiBundle\...\AddingEligibleProductVariantToCart` | `productVariantNotSufficient` | `productVariantNotSufficientMessage` |
   | `ApiBundle\...\ChangedItemQuantityInCart` | `productVariantNotLongerAvailable`, `productVariantNotSufficient` | `productVariantNotLongerAvailableMessage`, `productVariantNotSufficientMessage` |
   | `PromotionBundle\...\PromotionRuleType`, `PromotionActionType`, `CatalogPromotionActionType`, `CatalogPromotionScopeType` | `invalidType` | `invalidTypeMessage` |
   | `PaymentBundle\...\GatewayFactoryExists` | `invalidGatewayFactory` | `invalidGatewayFactoryMessage` |
   | `ShippingBundle\...\ShippingMethodCalculatorExists` | `invalidShippingCalculator` | `invalidShippingCalculatorMessage` |
   | `ShippingBundle\...\ShippingMethodRule` | `invalidType` | `invalidTypeMessage` |

3. Two new class-level constraints have been added to `Sylius\Component\Core\Model\AdminUser` (validation group `sylius`),
   see the [Admin users](#admin-users) section for details:

   - `Sylius\Bundle\CoreBundle\Validator\Constraints\AtLeastOneAccessLevel`
   - `Sylius\Bundle\CoreBundle\Validator\Constraints\CannotRevokeOwnAdministrationAccess`

## Admin users

1. Admin users have two independent **access levels**, both backed by roles:

   | Access level           | Role                          |
   |------------------------|-------------------------------|
   | Administration access  | `ROLE_ADMINISTRATION_ACCESS`  |
   | API access             | `ROLE_API_ACCESS`             |

   The `ROLE_API_ACCESS` role is available as `Sylius\Component\Core\Model\AdminUserInterface::API_ACCESS_ROLE`.

2. The following methods have been added to `Sylius\Component\Core\Model\AdminUserInterface`:

   ```php
   public function hasAdministrationAccess(): bool;
   public function setAdministrationAccess(bool $administrationAccess): void;
   public function hasApiAccess(): bool;
   public function setApiAccess(bool $apiAccess): void;
   ```

   If you have custom classes implementing this interface, you must add these methods. In
   `Sylius\Component\Core\Model\AdminUser` they are implemented on top of the roles listed above.

3. A new class-level constraint, `Sylius\Bundle\CoreBundle\Validator\Constraints\AtLeastOneAccessLevel`, has been added to
   `Sylius\Component\Core\Model\AdminUser`. It requires every admin user to hold at least one of the
   `ROLE_ADMINISTRATION_ACCESS` or `ROLE_API_ACCESS` roles.

   If your installation has existing admin users with neither role assigned, saving/updating them (via the admin panel,
   the API, or the `sylius:admin-user:create` command) will now fail validation until one of the two access levels is
   granted.

4. A new class-level constraint, `Sylius\Bundle\CoreBundle\Validator\Constraints\CannotRevokeOwnAdministrationAccess`,
   has been added to `Sylius\Component\Core\Model\AdminUser`. It prevents an admin user that has been granted
   administration access from revoking it on their own account, so that access to the admin panel cannot be lost
   permanently.

   It is validated by the `sylius.validator.cannot_revoke_own_administration_access` service, which relies on
   `security.token_storage` and `security.authorization_checker`. Admin users authenticated without administration
   access (for example API-only ones) are not affected, and neither are admin users edited by somebody else.

5. The `Sylius\Bundle\AdminBundle\Command\CreateAdminUser` command has been extended with two optional constructor
   arguments:

   ```diff
    public function __construct(
        private string $email,
        private string $username,
        private ?string $firstName,
        private ?string $lastName,
        private string $plainPassword,
        private string $localeCode,
        private bool $enabled,
   +    private bool $administrationAccess = true,
   +    private bool $apiAccess = false,
    )
   ```

   Their values are exposed by the new `hasAdministrationAccess()` and `hasApiAccess()` methods and are turned into
   roles by `Sylius\Bundle\AdminBundle\CommandHandler\CreateAdminUserHandler`. The defaults keep the previous
   behaviour, so dispatching the command without them creates an admin user with administration access only.

   The interactive `sylius:admin-user:create` command now additionally asks for the access levels (multiselect,
   administration access preselected) and shows them in the summary table.

6. The `sylius_admin_user` form type (`Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType`) has two new checkbox
   fields, `administrationAccess` and `apiAccess`, rendered by the `access_levels` form section. If you have overridden
   the admin user form template, add them to your own layout.

## Payment

1. The **Payment Request** feature is no longer **experimental**.

   The `@experimental` annotation has now been removed from all classes, interfaces, traits and attributes belonging
   to this feature across `Sylius\Component\Payment`, `Sylius\Bundle\PaymentBundle`, `Sylius\Bundle\PayumBundle`,
   the order pay flow in `Sylius\Bundle\CoreBundle\OrderPay` and `Sylius\Bundle\PayumBundle\OrderPay`, and the
   Payment Request layer in `Sylius\Bundle\ApiBundle`. These classes are now covered by the Sylius Backward
   Compatibility policy.

2. A new `countByGatewayFactoryName(string $factoryName): int` method has been added to
   `Sylius\Component\Payment\Repository\PaymentMethodRepositoryInterface` and implemented in
   `Sylius\Bundle\PaymentBundle\Doctrine\ORM\PaymentMethodRepository`.

   ```php
   public function countByGatewayFactoryName(string $factoryName): int;
   ```

   If you have a custom class implementing this interface without extending
   `Sylius\Bundle\PaymentBundle\Doctrine\ORM\PaymentMethodRepository`, you must add this method.

3. The admin payment method grid (`sylius_admin_payment_method`) has been redesigned. It is now defined in both
   `Sylius\Bundle\AdminBundle\Grid\PaymentMethodGrid` and
   `@SyliusAdminBundle/Resources/config/grids/payment_method.yml`, which are kept in sync.

   The following changes were applied to the grid fields:

   - the `code`, `gateway` and `enabled` columns have been **disabled** (`enabled: false`);
   - the `name` column now uses the `path: "."` option and a dedicated template
     `@SyliusAdmin/payment_method/grid/field/name.html.twig`, which renders the payment method together with its
     gateway logo, code, gateway and enabled status;
   - its label changed from `sylius.ui.name` to `sylius.ui.payment_method`.

   If you relied on the `code`, `gateway` or `enabled` columns being present (for example in templates, integrations
   or tests that read those columns), or if you overrode the `name` column, adjust your customization accordingly.

## Order

1. A new `getOrderAndItemPromotionTotal(): int` method has been added to
   `Sylius\Component\Core\Model\OrderInterface` and implemented in `Sylius\Component\Core\Model\Order`.

   ```php
   public function getOrderAndItemPromotionTotal(): int;
   ```

   If you have custom classes implementing this interface without extending `Sylius\Component\Core\Model\Order`,
   you must add this method.

   `getOrderPromotionTotal()` sums `ORDER_UNIT_PROMOTION_ADJUSTMENT`, `ORDER_ITEM_PROMOTION_ADJUSTMENT` and
   `ORDER_PROMOTION_ADJUSTMENT` together. The unit-level adjustment is already netted into each item's subtotal
   (`OrderItem::getSubtotal()`), which is what the "Items total" row of the cart summary displays. Showing it again
   in the "Discount" row subtracted it twice, so `Items total + Discount + shipping + tax` did not add up to the
   actual order total and the discount shown to the customer was larger than the one really applied.

   `getOrderAndItemPromotionTotal()` sums only `ORDER_ITEM_PROMOTION_ADJUSTMENT` and `ORDER_PROMOTION_ADJUSTMENT`,
   so it can safely be displayed next to "Items total".

2. The shop cart summary and checkout summary now use `getOrderAndItemPromotionTotal()` instead of
   `getOrderPromotionTotal()`:

   - `@SyliusShop/cart/index/content/form/sections/general/summary/discount.html.twig`
   - `@SyliusShop/checkout/common/sidebar/summary/total/promotion_total.html.twig`

   If you have custom templates, reports or integrations that display a promotion total next to
   `getItemsSubtotal()`/`getSubtotal()`, switch them to `getOrderAndItemPromotionTotal()` to avoid the same
   double-counting. `getOrderPromotionTotal()` is unchanged and still returns the promotion's full effect.

## Promotion

1. New **opt-in per-channel** promotion rule and action types have been added. They let a single
   promotion rule/action hold independent configuration per channel, with the `configuration` array
   keyed by channel code (e.g. `['WEB_US' => ['count' => 2], 'WEB_GB' => ['count' => 5]]`):

   - Rules: `cart_quantity_per_channel`, `customer_group_per_channel`, `nth_order_per_channel`,
     `shipping_country_per_channel`, `has_taxon_per_channel`, `contains_product_per_channel`.
   - Actions: `order_percentage_discount_per_channel`, `shipping_percentage_discount_per_channel`.

   These are **additional** types registered alongside the existing plain ones. Existing promotions
   and the plain rule/action types are unchanged, and **no core service is replaced**, so the change
   is fully backward compatible and requires no action.

   Implementation: two generic decorators in `Sylius\Component\Core\Promotion` unwrap the current
   channel's configuration slice and delegate to the standard checker/command:

   - `Sylius\Component\Core\Promotion\Checker\Rule\PerChannelRuleChecker`
   - `Sylius\Component\Core\Promotion\Action\PerChannelPromotionActionCommand`

   The admin forms use new `ChannelBased*ConfigurationType` form types built on top of
   `Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType`.

## Shop

1. The return type of `Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCartFormComponent::addToCart()` has been
   widened from `RedirectResponse` to `?Response`:

   ```diff
   -    ): RedirectResponse {
   +    ): ?Response {
   ```

   This allows overriding the live action with a custom response handling, e.g. returning `null` to skip the redirect
   and let the component re-render in place (adding to cart without a page reload). The default behavior is unchanged,
   a `RedirectResponse` is still returned.

   Existing subclasses overriding this method with the `RedirectResponse` return type remain valid thanks to
   return type covariance and require no changes.

## New Features

1. New post-flush cart events have been added to `Sylius\Component\Order\SyliusCartEvents`.

   The following events are now dispatched **after** `$manager->flush()`, allowing listeners to react once the cart has been safely persisted to the database:

   | New constant | Event name |
         |---|---|
   | `SyliusCartEvents::CART_ITEM_POST_ADD` | `sylius.cart_item_post_add` |
   | `SyliusCartEvents::CART_ITEM_POST_REMOVE` | `sylius.cart_item_post_remove` |
   | `SyliusCartEvents::CART_POST_CHANGE` | `sylius.cart_post_change` |
   | `SyliusCartEvents::CART_POST_CLEAR` | `sylius.cart_post_clear` |

   > **Note:** because these events are dispatched after the flush, their subjects no longer carry the full picture,
   > so the missing data is passed as event arguments:
   >
   > | Event | Why | Argument |
   > |---|---|---|
   > | `CART_ITEM_POST_REMOVE` | `CART_ITEM_REMOVE` triggers `OrderModifier::removeFromOrder()`, which calls `$orderItem->setOrder(null)`, so `$orderItem->getOrder()` returns `null` | `$event->getArgument('cart')` |
   > | `CART_POST_CLEAR` | Doctrine resets the identifier of a removed entity on flush, so `$cart->getId()` returns `null` | `$event->getArgument('cartId')` |

   Example usage — sending a cart to an external service only after it is stored:

   ```php
   use Sylius\Component\Order\SyliusCartEvents;
   use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
   use Symfony\Component\EventDispatcher\GenericEvent;

   #[AsEventListener(event: SyliusCartEvents::CART_ITEM_POST_ADD)]
   final class SendCartToMarketingAutomationListener
   {
       public function __invoke(GenericEvent $event): void
       {
           // Cart is already persisted — safe to send to external service.
       }
   }
   ```

   The existing pre-flush events (`CART_ITEM_ADD`, `CART_ITEM_REMOVE`, `CART_CHANGE`, `CART_CLEAR`) remain unchanged.

## Deprecations

1. Not passing `ComparisonOperatorMatcherInterface` to the following classes is deprecated since Sylius 2.3 and will no longer be supported in Sylius 3.0:

    - `Sylius\Bundle\PromotionBundle\Form\Type\Rule\CartQuantityConfigurationType`
    - `Sylius\Bundle\PromotionBundle\Form\Type\Rule\ItemTotalConfigurationType`
    - `Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker`
    - `Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker`

   Pass a `ComparisonOperatorMatcherInterface` when instantiating these classes directly. Symfony services are already configured to inject it.

2. The optional `$comparisonOperator` arguments have been added to `PromotionRuleFactory::createCartQuantity()` and `PromotionRuleFactory::createItemTotal()` without changing `PromotionRuleFactoryInterface` to preserve backward compatibility. They will be added to the interface in Sylius 3.0.

3. Not passing a `Symfony\Contracts\Translation\TranslatorInterface` to `Sylius\Component\User\Security\Checker\EnabledUserChecker` is deprecated since Sylius 2.3 and will be required in Sylius 3.0.

   ```diff
   -public function __construct()
   +public function __construct(private readonly ?TranslatorInterface $translator = null)
   ```

   > **Deprecated:** When a translator is provided, the "account is disabled" message shown during authentication uses the new `sylius.user.account_disabled` translation key (`validators` domain).
   > Without it, the checker falls back to the original `DisabledException('User account is disabled.')` behavior.

4. Passing a `Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface` directly to the following catalog-facing classes is deprecated since Sylius 2.3.
   Implement `Sylius\Component\Core\Calculator\CatalogPricesCalculatorInterface` instead, which extends `ProductVariantPricesCalculatorInterface` with no additional methods.
   It will be required in Sylius 3.0.

   Affected classes:
   - `Sylius\Bundle\CoreBundle\Twig\PriceExtension`
   - `Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer`
   - `Sylius\Bundle\ShopBundle\Twig\Component\Product\PriceComponent`
   - `Sylius\Bundle\ShopBundle\Twig\Component\Product\CardComponent`
   - `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantPriceMapProvider`
   - `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOriginalPriceMapProvider`
   - `Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantLowestPriceMapProvider`

   If you have a custom calculator used for catalog display, make it implement `CatalogPricesCalculatorInterface`:

   ```php
   use Sylius\Component\Core\Calculator\CatalogPricesCalculatorInterface;

   final class MyCustomCatalogPriceCalculator implements CatalogPricesCalculatorInterface
   {
       // ...
   }
   ```

   This allows you to decorate catalog display pricing independently from cart/order pricing
   (`sylius.order_processing.order_prices_recalculator`, `sylius.filter.promotion.price_range`),
   which remain on `ProductVariantPricesCalculatorInterface`.

2. The add to cart logic has been extracted from `Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCartFormComponent`
   into a new `Sylius\Bundle\OrderBundle\Adder\CartItemAdder` service (`sylius.adder.cart_item`):

   ```php
   interface CartItemAdderInterface
   {
       public function add(AddToCartCommandInterface $addToCartCommand): void;
   }
   ```

   The service dispatches the `SyliusCartEvents::CART_ITEM_ADD` event (which performs the actual cart modification),
   persists and flushes the cart, and then dispatches `SyliusCartEvents::CART_ITEM_POST_ADD`. Use it in custom add to
   cart components or controllers instead of duplicating this logic.

   Not passing a `Sylius\Bundle\OrderBundle\Adder\CartItemAdderInterface` instance as the last constructor argument of
   `AddToCartFormComponent` is deprecated since Sylius 2.3 and will be required in Sylius 3.0:

   ```diff
    public function __construct(
        // ...
        ProductRepositoryInterface $productRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
   +    protected readonly ?CartItemAdderInterface $cartItemAdder = null,
    ) {
   ```
