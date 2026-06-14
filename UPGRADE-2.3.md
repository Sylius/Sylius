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

`GridProviders` are now configurable, allowing users to choose between PHP and YAML for Sylius grid configurations. You can now change the definition format Sylius will use. Note that configurations in different formats cannot be merged, so if you switch to PHP for an existing grid, you will need to recreate the vendor's YAML definition.

Example configuration:

```yaml
sylius_core:
    grid:
        default_type: array # from the config directory (yaml or php)
        grids:
            sylius_admin_product_variant:
                use_legacy_config: true # from src directory, these are services tagged as "sylius.grid"
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

