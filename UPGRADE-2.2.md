# UPGRADE FROM `2.1` TO `2.2`

## Telemetry

Sylius 2.2.0 introduces anonymous telemetry to help us understand how Sylius is used and improve the platform.

**What data is collected:**
- Anonymous installation ID (hashed, non-reversible)
- Sylius and PHP versions, default locale
- Aggregated statistics as segments (broad ranges, not exact values):
   - Customers/products/variants count (e.g., "1K-10K", "100K-1M")
   - GMV and AOV ranges per month (e.g., "100K-500K", "50-100")

**No sensitive data is ever collected** - no customer information, no order details, no personal data.

**Configuration:**

Telemetry is enabled by default and uses a default salt for hashing the installation ID.

To disable telemetry, set the following environment variable in your `.env` file:

```dotenv
SYLIUS_TELEMETRY_ENABLED=0
```

To change the salt, set the `SYLIUS_TELEMETRY_SALT` environment variable:

```dotenv
SYLIUS_TELEMETRY_SALT=your-custom-salt
```

## Deprecations

1. Not injecting a `tagged_iterator` with the tag `sylius_shop.modifier.address_form_values` into the constructor of `Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent` is deprecated since Sylius 2.2 and will be required in Sylius 3.0.

   This change enables extending the checkout address form with custom fields or logic by registering services tagged with `sylius_shop.modifier.address_form_values`, which implement the `AddressFormValuesModifierInterface`.

```php
    public function __construct(
        OrderRepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly CustomerContextInterface $customerContext,
        protected readonly UserRepositoryInterface $shopUserRepository,
        protected readonly AddressRepositoryInterface $addressRepository,
+       protected readonly ?iterable $addressFormValuesModifiers = null,
    )
```

1. Direct usage of `loader.svg` and `loader.gif` assets is deprecated.
   Use `@SyliusAdmin/shared/helper/loader.html.twig` or `@SyliusShop/shared/macro/loader.html.twig` instead.

1. The `Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface::updateFromReview()` method has been deprecated and will be removed in Sylius 3.0. Use state machine mechanism implemented by Symfony Workflow instead.

## Translations

1. The `TranslationLocaleProvider` now ensures that the default locale (configured as `locale` in `config/parameters.yaml`)
   is always placed at the beginning of the returned locales array.  
   Other locales remain in the same order as returned by the repository.

## Grid providers are now configurable
`GridProviders` are now configurable, allowing users to choose between PHP and YAML for Sylius grid configurations. You can now change the definition format Sylius will use. Note that configurations in different formats cannot be merged, so if you switch to PHP for an existing grid, you will need to recreate the vendor's YAML definition.

Example configuration:

```yaml
sylius_core:
    grid:
        default_type: array # from the config directory (yaml or php)
        grids:
            sylius_admin_product_variant:
                type: 'service' # from src directory, these are services tagged as "sylius.grid"
```
