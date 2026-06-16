# UPGRADE FROM `2.2` TO `2.3`

## Configuration

1. The default value of `sylius_core.order_by_identifier` has been changed from `true` to `false`. ([#18956](https://github.com/Sylius/Sylius/pull/18956))

   The `OrderByIdentifierSqlWalker` is no longer enabled by default.
   If your application relies on ordering by identifier, enable it explicitly in your configuration:

   ```yaml
   sylius_core:
       order_by_identifier: true
   ```

## Grid providers are now configurable

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

   The `StringInflector::nameToSlug()` method has been **deprecated** and will be removed in Sylius 3.0.

2. The `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle` packages have been removed.
   
   The Gaufrette integration has been unusable as a filesystem adapter.
   Since Sylius 2.0 the default filesystem adapter uses Flysystem instead. 

   If your application depends on the Gaufrette packages directly, require them explicitly in your `composer.json`.

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

