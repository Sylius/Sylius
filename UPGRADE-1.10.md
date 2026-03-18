# UPGRADE FROM `v1.10.16` TO `v1.10.17`

### Telemetry improvements

Telemetry has been improved with per-query database timeouts to prevent slow queries from blocking admin panel
requests. Timeouts are applied automatically using platform-specific mechanisms (MySQL, MariaDB, PostgreSQL).

The global query timeout (in milliseconds, default: 60000, minimum: 1000) is **configurable** via environment variable:

```dotenv
SYLIUS_TELEMETRY_QUERY_TIMEOUT=30000
```

Additionally, telemetry collection is now rate-limited — it will be skipped if it was already triggered within
the last hour, preventing redundant data collection on rapid admin page loads.

# UPGRADE FROM `v1.10.15` TO `v1.10.16`

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

# UPGRADE FROM `v1.10.14` TO `v1.10.15`

## Telemetry

Sylius now collects anonymous usage data to help improve the platform. No personal or sensitive information is collected.

For more details, see the [Telemetry documentation](https://docs.sylius.com/the-book/configuration/telemetry).

# UPGRADE FROM `v1.10.12` TO `v1.10.13`

1. The support for Symfony 5.2 has been dropped, because it is not maintained version that has some security vulnerabilities. 
   The recommended Symfony version to use with Sylius is 5.4 as it is the current long-term support version.

2. `Order total` shipping rule has been changed to `Items total` and now it is based on items total instead of order total.

# UPGRADE FROM `v1.10.x` TO `v1.10.12`

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

# UPGRADE FROM `v1.10.8` TO `v1.10.10`

1. Field `createdByGuest` has been added to `Sylius\Component\Core\Model\Order`, this change will allow us to distinguish carts 
between guests and logged in customers.

2. Not passing `createdByGuestFlagResolver` through constructor in `Sylius\Component\Core\Cart\Context\ShopBasedCartContext` 
is deprecated in Sylius 1.10.9 and it will be prohibited in Sylius 2.0.

# UPGRADE FROM `v1.10.x` TO `v1.10.8`

1. Update `payum/payum` to `^1.7` and execute Doctrine Migrations

If `payum/payum` is a root requirement (in the project's `composer.json`), then run:

```shell
composer require payum/payum:^1.7
```

otherwise, run:

```shell
composer update payum/payum
```

then execute the migrations:

```shell
bin/console doctrine:migrations:migrate
```

# UPGRADE FROM `v1.10.0` TO `v1.10.1`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.9.X` TO `v1.10.0`

### Admin API Bundle Removal

Sylius v1.10 extracts AdminApiBundle outside the core package. You might choose either to keep that bundle or remove it in case it's not used.

#### Keeping Admin API Bundle

1. Add Admin API Bundle to your application by running the following command:

```
composer require sylius/admin-api-bundle
```

#### Removing Admin API Bundle

1. **Before installing Sylius 1.10**, run the following command to adjust the database schema:

```
bin/console doctrine:migrations:execute Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20161202011556 Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20170313125424 Sylius\\Bundle\\AdminApiBundle\\Migrations\\Version20170711151342 --down
```

1. After installing Sylius v1.10, remove the remaining configuration by following the changes in [this PR](https://github.com/Sylius/Sylius-Standard/pull/543/files):

- remove `friendsofsymfony/oauth-server-bundle` from your `composer.json` and run `composer update`
- remove `FOS\OAuthServerBundle\FOSOAuthServerBundle` and `Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle` from `config/bundles.php`
- remove `@SyliusAdminApiBundle/Resources/config/app/config.yml` import from `config/packages/_sylius.yaml`
- remove `sylius_admin_api` package configuration from `config/packages/_sylius.yaml`
- remove `oauth_token` and `api` firewalls from `config/security.yaml`
- remove `sylius.security.api_regex` parameter and all its usage in access control from `config/security.yaml`
- remove `config/routes/sylius_admin_api.yaml` file
- remove all classes from `src/Entity/AdminApi` directory

### Buses

1. Message buses `sylius_default.bus` and `sylius_event.bus` has been deprecated. Use `sylius.command_bus` and `sylius.event_bus` instead.

### Shop & Core Decoupled

1. `Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener` has been moved from CoreBundle to ShopBundle, renamed to `Sylius\Bundle\ShopBundle\EventListener\ShopCartBlamerListener` and adjusted to work properly when decoupled.

1. `Sylius\Bundle\CoreBundle\EventListener\UserCartRecalculationListener` has been moved from CoreBundle to ShopBundle as `Sylius\Bundle\ShopBundle\EventListener\UserCartRecalculationListener` and adjusted to work properly when decoupled.

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.10.md).
