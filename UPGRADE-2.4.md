# UPGRADE FROM `2.3` TO `2.4`

## Promotions

### Catalog promotions are recalculated on the affected variants only

Creating, updating, ending or disabling a catalog promotion used to dispatch the recalculation of **every variant of the catalog**.
It now dispatches the recalculation of the variants the promotion is currently applied on, plus the variants in the current scope of the promotion.
The prices and applied promotions of the variants are the same as before.

This relies on the new `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface`, which gets the variants from:

- one `Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesInScopeProviderInterface` per scope type (`for_taxons`, `for_products` and `for_variants` are provided by Sylius), registered with the `sylius.catalog_promotion.variant_codes_provider` tag and a `type` attribute,
- `Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProviderInterface` for the variants the promotion is applied on.

A provider may return more variants than the ones matched by the corresponding `VariantInScopeCheckerInterface`, but never fewer.

**Custom scope types.** If a promotion has a scope whose type has no provider, all variants of the catalog are recalculated, as before.
To benefit from the targeted recalculation, register a provider for your scope type:

```php
$services
    ->set(MyScopeVariantCodesProvider::class)
    ->tag('sylius.catalog_promotion.variant_codes_provider', ['type' => 'my_scope_type'])
;
```

**Behavior changes.** These are not backward compatibility breaks, but they may affect projects relying on the previous behavior:

1. A decorator of `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface` (service `sylius.processor.catalog_promotion.all_product_variant`)
   is no longer called when a catalog promotion is created, updated, ended or disabled, except as a fallback for promotions having a scope whose type has no provider.
   Decorate `TargetedCatalogPromotionProcessorInterface` (service `sylius.processor.catalog_promotion.targeted`) to hook into these events.

2. These events no longer recalculate the variants the promotion does not concern.
   A project that changes prices or product taxons outside of Sylius events (for example with a direct SQL import) has to trigger the recalculation itself,
   for instance through `AllProductVariantsCatalogPromotionsProcessorInterface::process()`.

## Deprecations

1. Not passing a `Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface` as the last constructor argument to the following classes is deprecated since Sylius 2.4 and will be required in Sylius 3.0:

    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionCreatedListener`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionUpdatedListener`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionEndedListener`
    - `Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\DisableCatalogPromotionHandler`

   > **Deprecated:** Without it, these classes keep recalculating every variant of the catalog through `AllProductVariantsCatalogPromotionsProcessorInterface`.
   > Symfony services are already configured to inject it.
