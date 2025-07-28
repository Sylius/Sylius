# UPGRADE FROM `2.1` TO `2.2`

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

### Routing

The routing path for the `sylius_admin_locale_delete` route has been updated:

```diff
- admin/locales/{id}
+ admin/locales/{id}/delete
```

The routing path for the `sylius_admin_product_review_accept` route has been updated:

```diff
- admin/product-review/{id}/accept
+ admin/product-reviews/{id}/accept
```

The routing path for the `sylius_admin_product_review_reject` route has been updated:

```diff
- admin/product-review/{id}/reject
+ admin/product-reviews/{id}/reject
```

The routing path for the `sylius_admin_product_variant_delete` route has been updated:

```diff
- admin/products/{productId}/variants/{id}
+ admin/products/{productId}/variants/{id}/delete
```

The routing path for the `sylius_admin_promotion_coupon_delete` route has been updated:

```diff
- /admin/promotions/{promotionId}/coupons/{id}
+ /admin/promotions/{promotionId}/coupons/{id}/delete
```


The routing path for the `sylius_admin_promotion_coupon_bulk_delete` route has been updated:

```diff
- /admin/promotions/{promotionId}/coupons/bulk-delete
+ /admin/promotions/{promotionId}/coupons/bulk_delete
```

### Translations

1. The `TranslationLocaleProvider` now ensures that the default locale (configured as `locale` in `config/parameters.yaml`)
   is always placed at the beginning of the returned locales array.  
   Other locales remain in the same order as returned by the repository.

