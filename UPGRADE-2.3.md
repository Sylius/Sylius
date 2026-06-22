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

   The `StringInflector::nameToSlug()` method has been **deprecated** and will be removed in Sylius 3.0.

2. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.
   
   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.

3. The `symfony/proxy-manager-bridge` and `friendsofphp/proxy-manager-lts` packages have been removed.

   They are no longer needed, lazy services now rely on PHP's native lazy proxies provided by
   `symfony/var-exporter` (the default since Symfony 6.4). No change is required in your application.

   If your application depends on these packages directly, require them explicitly in your `composer.json`.

4. The supported Doctrine version ranges have been **broadened** to allow the newer stack
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
