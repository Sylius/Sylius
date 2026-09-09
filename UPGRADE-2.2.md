# UPGRADE FROM `2.2.8` TO `2.2.9`

This is a **security release**. Updating is strongly recommended.

## Security fixes

### [Admin] Host header injection in the administrator password-reset e-mail link (High)

The link in the administrator password-reset e-mail was built with Twig's `url()` helper, which derives the scheme
and host from the incoming request. Anyone able to trigger such an e-mail — which requires nothing but the
administrator's e-mail address and a single unauthenticated request to `/admin/forgotten-password` or
`POST /api/v2/admin/administrators/reset-password` — could therefore make the link point at a domain of their choosing
by sending a spoofed `Host` (or `X-Forwarded-Host`) header, and capture the reset token when the administrator clicked
it. The shop-side password reset was never affected, as it builds its link from the channel's configured hostname.

The URL is now built in PHP from the current channel's `hostname`, which comes from the database. When no channel can
be resolved, the channel has no hostname set, or the admin route is not registered (headless installations), the
e-mail contains the bare reset token instead, which the administrator pastes into the shop manually.

**Changes in `Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManager`:**

```diff
 public function __construct(
     private SenderInterface $emailSender,
+    private RouterInterface $router,
+    private ChannelContextInterface $channelContext,
+    private bool $unsecuredUrls = false,
 )
```

If you have overwritten the `sylius.mailer.reset_password_email_manager` service or its arguments, check the correct
functioning.

# UPGRADE FROM `2.2.7` TO `2.2.8`

## UX Icons

1. The `ux:icons:lock` command (and the ux-icons cache warmer) imported `0` icons in Sylius applications.
   Symfony UX's `Symfony\UX\Icons\Twig\IconFinder` discovers icons by traversing the Twig loader, but it only
   understands Twig's `FilesystemLoader` and `ChainLoader`. Sylius decorates the Twig loader with
   `Sylius\Bundle\ThemeBundle\Twig\Loader\ThemedTemplateLoader`, which `IconFinder` cannot traverse, so no template
   was ever scanned.
   A new `Sylius\Bundle\UiBundle\DependencyInjection\Compiler\UxIconsIconFinderPass` now points the
   `.ux_icons.icon_finder` service at a dedicated Twig environment (`sylius_ui.ux_icons.twig_environment`) backed by
   the native filesystem loader (`twig.loader.native_filesystem`), so template scanning works again without affecting
   runtime template rendering.

# UPGRADE FROM `2.2.6` TO `2.2.7`

## Behat

1. The `waitForFormUpdate()` methods in the Behat page objects and elements now delegate to the new
   `Sylius\Behat\Service\DriverHelper::waitForLiveComponentUpdate()` helper.
   The previous implementation checked the `busy` attribute on the `form` element and relied on a fixed `sleep`,
   but Symfony UX Live Components set `busy` on the component root (`[data-controller~="live"]`), not on the form,
   so the wait was effectively a no-op. The helper now waits document-wide for `[busy]`/`[data-live-is-loading]`
   markers to appear and then disappear, without any hardcoded sleep.
   If you overrode `waitForFormUpdate()` in your own page objects, delegate to the helper as well.

2. The `live_form` element and the `waitForFormUpdate()` override were removed from
   `Sylius\Behat\Page\Admin\Order\UpdatePage`; it now inherits the shared implementation.

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

2. **Suggested payment methods banners** have been added to the payment methods index page.

   A carousel of banners promoting payment plugins (Stripe, PayPal, Adyen, Mollie) is now rendered above the grid.
   Each banner is a Twig hookable defined in
   `src/Sylius/Bundle/AdminBundle/Resources/config/app/twig_hooks/payment_method/index.yaml`.

   You can disable the whole banners carousel by disabling the `banner` hook in your application's Twig hooks
   configuration:

   ```yaml
   # config/packages/sylius_twig_hooks.yaml
   sylius_twig_hooks:
       hooks:
           'sylius_admin.payment_method.index.content':
               banner:
                   enabled: false
   ```

## Translations

1. The `TranslationLocaleProvider` now ensures that the default locale (configured as `locale` in `config/parameters.yaml`)
   is always placed at the beginning of the returned locales array.  
   Other locales remain in the same order as returned by the repository.
