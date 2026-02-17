# UPGRADE FROM `2.2` TO `2.3`

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

## Payment

1. The **Payment Request** feature is no longer **experimental**.

   The `@experimental` annotation has now been removed from all classes, interfaces, traits and attributes belonging
   to this feature across `Sylius\Component\Payment`, `Sylius\Bundle\PaymentBundle`, `Sylius\Bundle\PayumBundle`,
   the order pay flow in `Sylius\Bundle\CoreBundle\OrderPay` and `Sylius\Bundle\PayumBundle\OrderPay`, and the
   Payment Request layer in `Sylius\Bundle\ApiBundle`. These classes are now covered by the Sylius Backward
   Compatibility policy.

## Deprecations

1. Passing a `Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface` directly to the following catalog-facing classes is deprecated since Sylius 2.3.
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

## Routing

A few route paths have been updated:

| Name                                    | Path (before)                                  | Path (after)                                          |
|-----------------------------------------|------------------------------------------------|-------------------------------------------------------|
| sylius_admin_locale_delete              | `/admin/locales/{id}`                          | `/admin/locales/{id}/delete`                          |
| sylius_admin_product_review_accept      | `/admin/product-review/{id}/accept`            | `/admin/product-reviews/{id}/accept`                  |
| sylius_admin_product_review_reject      | `/admin/product-review/{id}/reject`            | `/admin/product-reviews/{id}/reject`                  |
| sylius_admin_product_variant_delete     | `/admin/products/{productId}/variants/{id}`    | `/admin/products/{productId}/variants/{id}/delete`    |
| sylius_admin_promotion_coupon_delete    | `/admin/promotions/{promotionId}/coupons/{id}` | `/admin/promotions/{promotionId}/coupons/{id}/delete` |
| sylius_admin_shop_user_delete           | `/admin/shop-user/{id}`                        | `/admin/shop-users/{id}/delete`                       |
| sylius_shop_account_address_book_delete | `/{_locale}/account/address-book/{id}`         | `/{_locale}/account/address-book/{id}/delete`         |
