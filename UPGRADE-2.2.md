# UPGRADE FROM `2.2.11` TO `2.2.12`

This is a **security release**. Updating is strongly recommended.

## Security fixes

### [Shop] Host header injection in the e-mail links of a channel without a hostname (High)

The link in the shop password-reset e-mail is built by the `sylius_channel_url` Twig function
(`Sylius\Bundle\CoreBundle\Twig\ChannelUrlExtension`) from the channel's configured `hostname`. When that field was
empty, the function fell back to the scheme and host of the incoming request. Triggering the e-mail requires nothing
but the customer's e-mail address and a single unauthenticated request to `/{_locale}/forgotten-password` or
`POST /api/v2/shop/customers/reset-password`, so on a single-channel store whose channel has no hostname an attacker
could make the link point at a domain of their choosing with a spoofed `Host` header, and capture the reset token when
the customer clicked it.

This corrects the statement in the `2.2.9` notes below that the shop-side password reset was never affected: it is
safe only when the channel has a hostname.

The request host is now used only when `framework.trusted_hosts` is configured, because Symfony then rejects any
request whose `Host` does not match before Sylius runs. Otherwise, when the channel has no hostname, the function
returns the path alone.

**Action required if any of your channels has no hostname.** On such a store, without `trusted_hosts`, every link in
every e-mail becomes a relative path after this update, the password-reset button included, and a relative link in an
e-mail leads nowhere. Before updating, do at least one of the following (both are recommended):

1. Set the hostname on each channel: **Configuration > Channels**, edit the channel, fill in **Hostname**
   (for example `shop.example.com`) and save.
2. Configure trusted hosts with a strict pattern for your domain(s). A permissive pattern such as `.*` satisfies the
   check but restores the vulnerable behaviour:

   ```yaml
   # config/packages/framework.yaml
   framework:
       trusted_hosts: ['^shop\.example\.com$']
   ```

   The same setting is available without touching `config/`: `SYMFONY_TRUSTED_HOSTS='^shop\.example\.com$'` in
   `.env.local`. Clear the cache afterwards.

`framework.trusted_proxies` is not a substitute. It tells Symfony which `X-Forwarded-*` headers to trust and does not
validate the host; adding `x-forwarded-host` to `trusted_headers` without `trusted_hosts` makes the problem worse.

If every channel in your store has a hostname, nothing changes for you.

If a channel has no hostname, the link can only be built from the current HTTP request. Where there is no request,
for example in a console command or a Messenger worker that renders e-mails, the function now returns a relative path
(before this fix it used `framework.router.default_uri`). `trusted_hosts` does not help there. If you render e-mails
outside a request, set the hostname on the channel.

**Changes in `Sylius\Bundle\CoreBundle\Twig\ChannelUrlExtension`:**

```diff
 public function __construct(
     ChannelContextInterface $channelContext,
     UrlHelper $urlHelper,
     bool $unsecuredUrls = false,
+    ?RequestStack $requestStack = null,
 )
```

The `sylius.twig.extension.channel_url` service now passes `request_stack` as the fourth argument. If you have
overwritten that service or its arguments, add the argument, otherwise the extension cannot see the request and returns
relative paths whenever the channel has no hostname.

### [API] Payment request could be created with a payment method not available for the order (Medium)

`POST /api/v2/shop/orders/{tokenValue}/payment-requests` accepted any `paymentMethodCode` that exists in the store,
including payment methods that are disabled or not assigned to the order's channel, so a customer or a guest holding
the order token could route the payment of their own order through a gateway the merchant did not offer there. The
sibling endpoints for choosing and changing a payment method already rejected such methods with HTTP 422.

The `AddPaymentRequest` command is now validated by the same `ChosenPaymentMethodEligibility` constraint and the
endpoint answers 422 for a method that is not available for the payment. `AddPaymentRequestHandler` additionally
re-checks the method against `PaymentMethodsResolverInterface` and throws `PaymentMethodNotFoundException` when it is
not supported, which covers commands dispatched directly on the command bus.

**Changes in `Sylius\Bundle\ApiBundle\CommandHandler\Payment\AddPaymentRequestHandler`:**

```diff
 public function __construct(
     ...
     private ?UserContextInterface $userContext = null,
+    private ?PaymentMethodsResolverInterface $paymentMethodsResolver = null,
 )
```

The `sylius_api.command_handler.payment.add_payment_request` service now passes `sylius.resolver.payment_methods` as
the last argument. If you have overwritten that service or its arguments, add the argument. Without it the HTTP
endpoint is still protected by the validator, but the handler-level check is skipped.

# UPGRADE FROM `2.2.9` TO `2.2.10`

## Deprecations

1. The `$addressRepository` argument of `Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent`
   is no longer used. It is now nullable and optional, passing it triggers a deprecation, and the Sylius service
   definition no longer passes it.

   Drop it from your own service definitions. Classes extending the component must stop reading the
   `$addressRepository` property: it is `null` unless your own definition passes it. Both the argument and the
   property will be removed in Sylius `3.0`.

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
