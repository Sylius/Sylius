# UPGRADE FROM `2.2.6` TO `2.2.7`

## Constructor Signature Changes

1. The constructor of `Sylius\Bundle\ApiBundle\ApiPlatform\Routing\IriConverter` has been extended with an optional `ApiPlatform\Metadata\ResourceClassResolverInterface` argument.

```php
    public function __construct(
        IriConverterInterface $decoratedIriConverter,
        PathPrefixProviderInterface $pathPrefixProvider,
        OperationResolverInterface $operationResolver,
        RouterInterface $router,
+       ?ResourceClassResolverInterface $resourceClassResolver = null,
    )
```

## Bahavior changes

1. The `LiveComponentTagPass` and `TwigComponentTagPass` in `SyliusUiBundle` were registered with a priority of `500`,
   which caused them to run before Symfony's autoconfiguration passes (priority `100`).
   As a result, services tagged via `#[AutoconfigureTag]` or `registerForAutoconfiguration()` with the `sylius.twig_component`
   or `sylius.live_component.*` tag did not receive the `twig.component` tag.
   The priority has been lowered to `50` to ensure Symfony's autoconfiguration runs first.

## Order payment state recovery after authorized payment cancellation

When an authorized `Payment` transitions to `cancelled` (e.g. the merchant voids the authorization
via the payment gateway), the `Order.paymentState` now automatically recovers from `authorized` to
`awaiting_payment`, allowing the customer to retry payment.

Previously the order was left in an inconsistent state — `payment_state = authorized` even though
the authorization no longer existed — and the customer could not retry.

### Impact on custom code

**Symfony Workflow** — the `sylius_order_payment` workflow gains two new source states for the
`request_payment` transition:

```yaml
# Before
request_payment:
    from: [cart]
    to: awaiting_payment

# After
request_payment:
    from: [cart, authorized, partially_authorized]
    to: awaiting_payment
```

If you override this transition in your application config, add `authorized` and
`partially_authorized` to the `from` list.

**winzou_state_machine** — the same change applies to
`config/app/winzou_state_machine/sylius_order_payment.yml`.

**Custom `OrderPaymentStateResolver`** — if you have overridden `getTargetTransition()`, add
handling for the recovery case: when only `cart` or `new` payments exist on the order (all previous
payments are cancelled/failed), return `OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT`.

---

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

## Admin UI

1. A new `modal-portal.js` script has been added to `AdminBundle`.
   It moves Bootstrap modal elements to `<body>` before they are displayed,
   preventing them from being rendered behind the Bootstrap backdrop when nested inside a CSS stacking context (e.g. the sticky `.page-header`).

## Routing

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

## Translations

1. The `TranslationLocaleProvider` now ensures that the default locale (configured as `locale` in `config/parameters.yaml`)
   is always placed at the beginning of the returned locales array.  
   Other locales remain in the same order as returned by the repository.
