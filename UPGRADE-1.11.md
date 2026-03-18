# UPGRADE FROM `v1.11.17` TO `v1.11.18`

### Telemetry improvements

Telemetry has been improved with per-query database timeouts to prevent slow queries from blocking admin panel
requests. Timeouts are applied automatically using platform-specific mechanisms (MySQL, MariaDB, PostgreSQL).

The global query timeout (in milliseconds, default: 60000, minimum: 1000) is **configurable** via environment variable:

```dotenv
SYLIUS_TELEMETRY_QUERY_TIMEOUT=30000
```

Additionally, telemetry collection is now rate-limited — it will be skipped if it was already triggered within
the last hour, preventing redundant data collection on rapid admin page loads.

# UPGRADE FROM `v1.11.16` TO `v1.11.17`

This is a **security release** addressing multiple vulnerabilities. Updating is strongly recommended.

## Security fixes

### [API] DQL Injection via API Order Filters (Critical)

An unauthenticated DQL injection vulnerability has been fixed in the following API order filters:

- `Sylius\Bundle\ApiBundle\Filter\Doctrine\ProductPriceOrderFilter`
- `Sylius\Bundle\ApiBundle\Filter\Doctrine\TranslationOrderNameAndLocaleFilter`

Previously, user-supplied sort direction values (e.g. `order[price]`, `order[translation.name]`) were passed directly
into DQL `ORDER BY` clauses without validation, allowing an attacker to inject arbitrary DQL expressions.

Both filters now define an `ALLOWED_DIRECTIONS` whitelist (`['asc', 'desc']`) and validate the input against it
before applying it to the query. Invalid values are silently ignored.

**Changes in `ProductPriceOrderFilter`:**

```diff
 final class ProductPriceOrderFilter extends AbstractContextAwareFilter
 {
+    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];
+
     protected function filterProperty(/* ... */)
     {
         // ...
+        $direction = strtolower($value['price']);
+        if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
+            return;
+        }
         // ...
-        ->orderBy('channelPricing.price', $value['price'])
+        ->orderBy('channelPricing.price', $direction)
     }
 }
```

**Changes in `TranslationOrderNameAndLocaleFilter`:**

```diff
 final class TranslationOrderNameAndLocaleFilter extends AbstractContextAwareFilter
 {
+    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];
+
     protected function filterProperty(/* ... */)
     {
         // ...
-        $direction = $value['translation.name'];
+        $direction = strtolower($value['translation.name']);
+        if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
+            return;
+        }
         // ...
-        ->orderBy('translation.name', $value['translation.name'])
+        ->orderBy('translation.name', $direction)
     }
 }
```

**No action required** — this fix is applied automatically upon updating. If you have extended or overridden either
of these filters, verify that your custom implementation also validates the sort direction.

### [Promotion] Race condition on promotion usage limit (High)

A race condition has been fixed where concurrent orders could exceed a promotion's usage limit. When multiple checkouts
completed simultaneously, the non-atomic read-then-write of `Promotion::$used` allowed the counter to be incremented
beyond `usageLimit`.

1. A new class has been introduced:

   `Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier`

   This class implements `Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface` and uses
   atomic SQL statements (`UPDATE ... WHERE used < usage_limit` and `SELECT ... FOR UPDATE`) to enforce promotion
   and coupon usage limits at the database level, preventing race conditions.

   Its constructor accepts a `Doctrine\DBAL\Connection`:

   ```php
   public function __construct(Connection $connection)
   ```

2. The new service **decorates** the existing `sylius.promotion_usage_modifier` service:

   ```xml
   <service
       id="sylius.promotion_usage_modifier.atomic"
       class="Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier"
       decorates="sylius.promotion_usage_modifier"
   >
       <argument type="service" id="doctrine.dbal.default_connection" />
   </service>
   ```

   If you have overridden or decorated the `sylius.promotion_usage_modifier` service, review your customizations
   to ensure compatibility with the new decorator chain.

3. A new exception class has been introduced:

   `Sylius\Component\Core\Promotion\Exception\PromotionUsageLimitReachedException`

   This exception extends `Doctrine\ORM\OptimisticLockException` and is thrown when a promotion or coupon usage
   limit has been reached during checkout. It provides two named constructors:

   ```php
   PromotionUsageLimitReachedException::withPromotionCode(string $code): self
   PromotionUsageLimitReachedException::withCouponCode(string $code): self
   ```

   If you have custom error handling around the checkout completion workflow (e.g. in state machine callbacks
   or event listeners), you may want to catch this exception to display an appropriate message to the customer.

### XSS in dynamic inputs in shop and admin (High)

A stored XSS vulnerability has been fixed in multiple JavaScript components that rendered user-controlled data
without escaping.

A new sanitizer utility has been introduced:

`Sylius\Bundle\UiBundle\Resources\private\js\sylius-sanitizer.js`

```js
export function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}
```

**Affected files:**

- `sylius-lazy-choice-tree.js` — taxon tree leaf node names and codes are now sanitized before DOM insertion.
- `sylius-auto-complete.js` — autocomplete choice names and values are now sanitized.
- `sylius-product-auto-complete.js` — product autocomplete names and codes are now sanitized.
- `sylius-province-field.js` — province field values are now sanitized before insertion into HTML attributes.

**No action required** — this fix is applied automatically upon updating. If you have custom JavaScript that renders
API response data using jQuery DOM insertion or string templates, review your code for similar XSS vectors.

### [API] Disable shop GET adjustment endpoint (Medium)

The `api/v2/shop/adjustments/{id}` endpoint has been disabled. This endpoint exposed adjustment details
(including internal pricing data) to unauthenticated shop users.

The endpoint now returns a 404 response using `ApiPlatform\Core\Action\NotFoundAction`.

If your application depends on this endpoint, you will need to re-enable it by overriding the API Platform
resource configuration for `Adjustment`.

### Open Redirect vulnerability (Medium)

An open redirect vulnerability has been fixed in `CurrencySwitchController`, `ImpersonateUserController` and
`StorageBasedLocaleSwitcher`. These controllers no longer use the HTTP `Referer` header for redirects. Instead,
they use the `RouterInterface` to generate a redirect URL based on the `_sylius.redirect` route attribute
(defaulting to `sylius_shop_homepage`).

A new trait has been added: `Sylius\Bundle\ShopBundle\Controller\RedirectTrait`.

**Affected classes:**

- `Sylius\Bundle\ShopBundle\Controller\CurrencySwitchController`
- `Sylius\Bundle\AdminBundle\Controller\ImpersonateUserController`
- `Sylius\Bundle\ShopBundle\Locale\StorageBasedLocaleSwitcher`

If your application relied on the Referer-based redirect behavior, you can customize the redirect target
by overriding the route definition:

```yaml
sylius_shop_switch_currency:
    path: /{_locale}/switch-currency/{code}
    methods: [GET]
    defaults:
        _controller: sylius.controller.shop.currency_switch:switchAction
        _sylius:
            redirect: sylius_shop_homepage
```

## Other changes

### Fix too frequent/long requests to GUS

The `Sylius\Bundle\AdminBundle\Controller\NotificationController` has been updated to reduce the frequency and
duration of outbound requests to GUS.

1. The constructor of `NotificationController` has been modified:

   ```diff
    public function __construct(
        private ClientInterface $client,
        private MessageFactory $messageFactory,
        string $hubUri,
        private string $environment,
   +    private CacheItemPoolInterface $cache,
    )
   ```

   The `cache.app` service is injected as the new argument.

2. Responses are now **cached for 24 hours** (`TTL = 86400s`) using PSR-6 `CacheItemPoolInterface`.
   Subsequent calls to `getVersionAction()` return the cached result without making an HTTP request.

3. HTTP request timeouts have been added:

   ```diff
   -$hubResponse = $this->client->send($hubRequest, ['verify' => false]);
   +$hubResponse = $this->client->send($hubRequest, [
   +    'verify' => false,
   +    'timeout' => 2,
   +    'connect_timeout' => 1,
   +]);
   ```

   If you have overridden the `sylius.controller.admin.notification` service or its arguments, update your
   configuration to include the new `CacheItemPoolInterface` argument.

# UPGRADE FROM `v1.11.15` TO `v1.11.16`

## Telemetry

Sylius now collects anonymous usage data to help improve the platform. No personal or sensitive information is collected.

For more details, see the [Telemetry documentation](https://docs.sylius.com/the-book/configuration/telemetry).

# UPGRADE FROM `v1.11.11` TO `v1.11.12`

1. All entities and their relationships have a default order by identifier if no order is specified. You can disable
this behavior by setting the `sylius_core.order_by_identifier` parameter to `false`:
```yaml
sylius_core:
    order_by_identifier: false
```

# UPGRADE FROM `v1.11.7` TO `v1.11.8`

1. Cloning `Sylius\Component\Order\Model\Adjustment` resets values of fields `id`, `createdAt` and `updatedAt`.

# UPGRADE FROM `v1.11.6` TO `v1.11.7`

1. Method `Sylius\Component\Channel\Repository\ChannelRepository::findOneByHostname` has become deprecated, use
`Sylius\Component\Channel\Repository\ChannelRepository::findOneEnabledByHostname` instead. Simultaneously with this change
`Sylius\Component\Channel\Context\RequestBased\HostnameBasedRequestResolver::findChannel` will start selecting only a channel from a range
of enabled channels.

2. The `code` field was removed from OrderItem serialization (in `src/Sylius/Bundle/ApiBundle/Resources/config/serialization/OrderItem.xml`)
as such field does not exist. Please, add it in your code base if you need it.

# UPGRADE FROM `v1.11.2` TO `v1.11.3`

1. Order Processors' priorities have changed and `sylius.order_processing.order_prices_recalculator` has now a higher priority than `sylius.order_processing.order_shipment_processor`.

Previous priorities:
```shell
sylius.order_processing.order_adjustments_clearer          60         Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer
sylius.order_processing.order_shipment_processor           50         Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor
sylius.order_processing.order_prices_recalculator          40         Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator
...
```

Current priorities:
```shell
sylius.order_processing.order_adjustments_clearer          60         Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer
sylius.order_processing.order_prices_recalculator          50         Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator
sylius.order_processing.order_shipment_processor           40         Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor
...
```

If you rely on previous priorities, you can bring them back by setting flag ``sylius_core.process_shipments_before_recalculating_prices`` to ``true`` in ``config/packages/_sylius.yaml``:
```yaml
sylius_core:
    process_shipments_before_recalculating_prices: true
```
However, it is not recommended because new priorities fix [invalid estimated shipping costs](https://github.com/Sylius/Sylius/pull/13769).

# UPGRADE FROM `v1.10.X` TO `v1.11.0`

## Preconditions

### PHP 8.0 support

Sylius v1.11 comes with bump of minimal dependencies of PHP to v8.0. We strongly advice to make upgrade process step by step,
so it is highly recommended updating your PHP version being still on Sylius v1.10, as it is supporting both PHP7.4 and PHP8.0.

After ensuring, that previous step succeed, you may move forward to the Sylius v1.11 update.

## Main update

### "pagerfanta/pagerfanta" semantic_ui_translated removed

The `pagination.html.twig` has been changed to use the Twig view.

There are differences in the markup between the PHP template and the Twig template.

The wrapping container from 2.x branch of Pagerfanta in the PHP template was:

```html
<div class="ui stackable fluid pagination menu">
```

while in the Twig template in 3.x branch it's:

```html
<div class="ui pagination menu">
```

The "stackable" class affects responsive display and "fluid" affects whether the pagination menu is full-width.

### "polishsymfonycommunity/symfony-mocker-container" moved to dev-requirements

This obvious dev dependency was part of Sylius requirements. In 1.11 we've moved it to proper place. However, 
it may lead to app break, as this container could be used in your Kernel, if you used Sylius-Standard as your template. 
In such a case, please update your `src/Kernel.php` class as follows:

```diff
     protected function getContainerBaseClass(): string
     {
-        if ($this->isTestEnvironment()) {
+        if ($this->isTestEnvironment() && class_exists(MockerContainer::class)) {
            return MockerContainer::class;
         }
 
         return parent::getContainerBaseClass();
     }
```

If you were using MockerContainer in your app, you should also execute the following command:

```bash
composer req --dev polishsymfonycommunity/symfony-mocker-container
```

### API Platform required folders

If you don't already have, add an empty directory `api_platform` in your `config` directory and customize there any API resources.

### Minimum price & Promotions

We added MinimumPrice to channelPricings entity, this price should be taken into account when customizing any promotions in Sylius.
All calculating and distributing services provided by default depends on MinimumPrice.

### Calendar & Shipping

Service `sylius.calendar` has been deprecated. Use `Sylius\Calendar\Provider\DateTimeProviderInterface` instead.

Add a new bundle to your list of used bundles in `config/bundles.php` if they are not already there:

    ```diff
    +   Sylius\Calendar\SyliusCalendarBundle::class => ['all' => true],
    ```

### Order prices recalculator

Passing a `Sylius\Component\Core\Calculator\ProductVariantPriceCalculatorInterface` to `Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator` 
constructor is deprecated since Sylius 1.11 and will be prohibited in 2.0. Use `Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface` instead.

### Messenger transport

If you don't already have configured the messenger transport, configure it according to your needs by setting an environment variable `MESSENGER_TRANSPORT_DSN`.

### Behat

- Service `sylius.behat.context.hook.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Hook\CalendarContext` instead.
- Service `sylius.behat.context.setup.calendar` has been removed, use in your suites `Sylius\Calendar\Tests\Behat\Context\Setup\CalendarContext` instead.

### Potential BC-breaks

#### State machine callbacks

In Sylius we are using WinzouStateMachine where as example `sylius_order` state machine has declared 14 callbacks on one state.
If this will be customized and number of callbacks comes up to 16 and higher - the priority of callbacks will become randomized.

Sylius state machine callbacks from now on will have priorities declared. Ending at -100 with step of 100.
Please note that those priorities are being executed in ascending order. You can find all the new priorities at
`Sylius/Bundle/CoreBundle/Resources/config/app/state_machine`.

Be aware that if those priorities were customized, this would lead to problems. 
You should check and adjust priorities on your application.

#### Promoted properties from PHP 8.0

We've introduced promoted properties all over the code where it was possible. Please, pay attention especially to these classes:
- `Sylius\Bundle\AdminBundle\Controller\CustomerStatisticsController`
- `Sylius\Bundle\AdminBundle\Controller\Dashboard\StatisticsController`
- `Sylius\Bundle\AdminBundle\Controller\DashboardController`
- `Sylius\Bundle\ShopBundle\Controller\ContactController`
- `Sylius\Bundle\ShopBundle\Controller\CurrencySwitchController`
- `Sylius\Bundle\ShopBundle\Controller\HomepageController`
- `Sylius\Bundle\ShopBundle\Controller\LocaleSwitchController`
- `Sylius\Bundle\ShopBundle\Controller\SecurityWidgetController`
- `Sylius\Bundle\UiBundle\Controller\SecurityController`

In all of them constructor argument `$templatingEngine`, previously typed as `object` was changed to `EngineInterface|Environment`.
It should not cause any problems (only such services would work in these controllers), but is theoretically making the type
requirement stricter.

#### Form type extensions

All form type extensions supplied by Sylius now specify a priority of 100, instead of relying on the default value of 0.
This means that your form type extensions, including autowired ones, may now consistently override the effect of these
stock form type extensions without you having to explicitly specify their priorities. However, if you relied on the old
default values, you might have to review priorities of your own form type extensions, as well as any that you have overridden.
Please note that **unlike state machine callbacks**, form extension priorities are being executed in descending order. 

#### Channel pricing view

All form fields are now hardcoded into the `AdminBundle/Resources/views/ProductVariant/Tab/_channelPricings.html.twig` view.
If you have custom properties which were previously rendered automatically you now have to override this view in `templates/bundles/SyliusAdminBundle/ProductVariant/Tab/_channelPricings.html.twig`.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.11.md).
