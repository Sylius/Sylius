# UPGRADE FROM `2.1` TO `2.2`

### API Configuration

1. **Selective API Endpoints** - You can now enable/disable admin and shop API endpoints separately for better security and performance.

   New configuration options in `sylius_api`:

   ```yaml
   sylius_api:
       enabled: true
       endpoints:
           admin_enabled: true   # Enable/disable admin API endpoints
           shop_enabled: true    # Enable/disable shop API endpoints
   ```

   **Use Cases**:

   - **Headless shop** (disable admin endpoints for security):
     ```yaml
     sylius_api:
         enabled: true
         endpoints:
             admin_enabled: false
             shop_enabled: true
     ```

   - **Admin-only application**:
     ```yaml
     sylius_api:
         enabled: true
         endpoints:
             admin_enabled: true
             shop_enabled: false
     ```

### Deprecations

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

### Translations

1. The `TranslationLocaleProvider` now ensures that the default locale (configured as `locale` in `config/parameters.yaml`)
   is always placed at the beginning of the returned locales array.  
   Other locales remain in the same order as returned by the repository.
