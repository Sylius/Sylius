# UPGRADE FROM `2.2` TO `2.3`

## Configuration

1. The default value of `sylius_core.order_by_identifier` has been changed from `true` to `false`. ([#18956](https://github.com/Sylius/Sylius/pull/18956))

   The `OrderByIdentifierSqlWalker` is no longer enabled by default.
   If your application relies on ordering by identifier, enable it explicitly in your configuration:

   ```yaml
   sylius_core:
       order_by_identifier: true
   ```

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

## Promotion

1. Promotion rules and actions can now be configured **independently per channel**. The feature is additive — existing
   promotions keep working unchanged — but two core services had their **class swapped** by `OverridePromotionServicesPass`.
   The service ids, the implemented interfaces and the constructor arguments are **unchanged**:

   | Service id | Old class | New class |
   |------------|-----------|-----------|
   | `sylius.checker.promotion.rules_eligibility` | `Sylius\Component\Promotion\Checker\Eligibility\PromotionRulesEligibilityChecker` | `Sylius\Component\Core\Promotion\Checker\Eligibility\ChannelAwarePromotionRulesEligibilityChecker` |
   | `sylius.action.applicator.promotion` | `Sylius\Component\Promotion\Action\PromotionApplicator` | `Sylius\Component\Core\Promotion\Action\ChannelAwarePromotionApplicator` |

   If you **decorate** these services, no change is required. If you **replaced** their definition or relied on the
   concrete class (e.g. `instanceof PromotionApplicator`), update your code to the new classes or to the
   `Sylius\Component\Promotion\Action\PromotionApplicatorInterface` /
   `Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface` abstractions.

2. A reserved configuration key `_excludedChannels`
   (`Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY`) may now be
   present in a promotion rule's or action's `configuration` array. It holds the channel codes for which the rule/action
   is skipped and is stripped before the configuration reaches the rule checker / action command.

   If you read a promotion rule/action `configuration` directly (custom rule checkers, action commands, serializers,
   exports), ignore this key — it is not part of a rule/action's own configuration.

3. New `*_per_channel` promotion rule and action types were added (e.g. `cart_quantity_per_channel`,
   `customer_group_per_channel`, `order_percentage_discount_per_channel`). Their configuration is keyed by channel code,
   so each channel can have its own values. These are additional types and do not replace the existing ones.

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
