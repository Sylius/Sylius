# UPGRADE FROM `v1.9.12` TO `v1.9.13`

### Telemetry improvements

Telemetry has been improved with per-query database timeouts to prevent slow queries from blocking admin panel
requests. Timeouts are applied automatically using platform-specific mechanisms (MySQL, MariaDB, PostgreSQL).

The global query timeout (in milliseconds, default: 60000, minimum: 1000) is **configurable** via environment variable:

```dotenv
SYLIUS_TELEMETRY_QUERY_TIMEOUT=30000
```

Additionally, telemetry collection is now rate-limited — it will be skipped if it was already triggered within
the last hour, preventing redundant data collection on rapid admin page loads.

# UPGRADE FROM `v1.9.11` TO `v1.9.12`

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

# UPGRADE FROM `v1.9.10` TO `v1.9.11`

## Telemetry

Sylius now collects anonymous usage data to help improve the platform. No personal or sensitive information is collected.

For more details, see the [Telemetry documentation](https://docs.sylius.com/the-book/configuration/telemetry).

# UPGRADE FROM `v1.9.5` TO `v1.9.6`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.8.X` TO `v1.9.0`

### Package upgrades

1. Upgrade the version of `friendsofsymfony/oauth-server-bundle` by:

    ```bash
    composer require "friendsofsymfony/oauth-server-bundle":">2.0.0-alpha.0 ^2.0@dev"
    ```

1. We've upgraded Sylius' ResourceBundle and GridBundle packages which forced us to upgrade major versions of our dependencies.

   Please follow [ResourceBundle's upgrade instructions](https://github.com/Sylius/SyliusResourceBundle/blob/master/UPGRADE.md#from-16x-to-17x).

   Apart from that, JMS Serializer major version upgrade requires to replace `array` type to `iterable` when serializing Doctrine Collections.

   Due to FOS Rest Bundle major version upgrade, the JSON error responses might have changed. If your tests stop passing,
   you can bring back old behaviour by overriding `error.json.twig` and `exception.json.twig` templates. You can check
   how we've done that in Sylius by looking into vendor code in `templates/bundles/TwigBundle/Exception/` directory.

1. We've replaced deprecated Doctrine Persistence API with the new one.

   Replace `Doctrine\Common\Persistence` namespace in your codebase to `Doctrine\Persistence`.

1. **We've removed DoctrineCacheBundle from our required packages while upgrading to the next major version of DoctrineBundle (v2).**

1. **We've upgraded SyliusThemeBundle to the next major version (v2.1).**

   Please follow [SyliusThemeBundle's upgrade instructions](https://github.com/Sylius/SyliusThemeBundle/blob/master/UPGRADE.md).

1. We've replaced deprecated Symfony Translator API with the new one.

   Replace `Symfony\Component\Translation\TranslatorInterface` with `Symfony\Contracts\Translation\TranslatorInterface` in your codebase.

1. Add proper redirect to changing password page in your `config/routes/sylius_shop.yaml` file:

    ```diff
    +   # see https://web.dev/change-password-url/
    +   sylius_shop_request_password_reset_token_redirect:
    +       path: /.well-known/change-password
    +       methods: [GET]
    +       controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::redirectAction
    +       defaults:
    +           route: sylius_shop_request_password_reset_token
    +           permanent: false
    ```

1. Add new bundles to your list of used bundles in `config/bundles.php` if they are not already there:

    ```diff
    +   BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
    +   SyliusLabs\Polyfill\Symfony\Security\Bundle\SyliusLabsPolyfillSymfonySecurityBundle::class => ['all' => true],
    ```

1. Remove `getContainerLoader` method from `src/Kernel.php` class if you did not customise it.

#### Upgrades Symfony to v5.2

1. Upgrade Symfony dependencies by:

    ```bash
    composer config extra.symfony.require "^5.2"
    composer require --dev "symfony/browser-kit":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/debug-bundle":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/intl":"^5.2" --no-update --no-scripts
    composer require --dev "symfony/web-profiler-bundle":"^5.2" --no-update --no-scripts
    composer update
    ```

1. We've removed the support for Symfony's Templating component (which is removed in Symfony 5).

    * Remove `templating` from framework's configuration:

        ```diff
        # config/packages/framework.yaml
       
        framework:
            # ...
        -    templating: { engines: ["twig"] }
        ```

    * Replace any usages of `Symfony\Bundle\FrameworkBundle\Templating\EngineInterface` with `Twig\Environment`.

      Inject `twig` service into your controllers instead of `templating` or `templating.engine.twig`.

      `$templating->renderResponse(...)` might be replaced with `new Response($twig->render(...))`.

1. Remove Twig route configuration from your `config/routes/dev/twig.yaml`:

    ```diff
    -   _errors:
    -       resource: '@TwigBundle/Resources/config/routing/errors.xml'
    -       prefix: /_error
    ```

1. Replace and add new keys in `config/packages/dev/jms_serializer.yaml`:

    ```diff
        jms_serializer:
            visitors:
    -           json:
    +           json_serialization:
                   options:
                       - JSON_PRETTY_PRINT
                       - JSON_UNESCAPED_SLASHES
                       - JSON_PRESERVE_ZERO_FRACTION
    +           json_deserialization:
    +              options:
    +                  - JSON_PRETTY_PRINT
    +                  - JSON_UNESCAPED_SLASHES
    +                  - JSON_PRESERVE_ZERO_FRACTION
    ```

1. Replace and add new keys in `config/packages/prod/jms_serializer.yaml`:

    ```diff
        jms_serializer:
            visitors:
    -           json:
    +           json_serialization:
                   options:
                       - JSON_UNESCAPED_SLASHES
                       - JSON_PRESERVE_ZERO_FRACTION
    +           json_deserialization:
    +              options:
    +                  - JSON_UNESCAPED_SLASHES
    +                  - JSON_PRESERVE_ZERO_FRACTION
    ```

1. Replace key in `config/packages/jms_serializer.yaml`:

   ```diff
       jms_serializer:
           visitors:
   -           xml:
   +           xml_serialization:
   ```

1. `config/packages/fos_rest.yaml` rules have been changed to:

    ```diff
        rules:
    -       - { path: '^/api/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    +       - { path: '^/api/v1/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    ```

### Data migrations

1. The `CoreBundle/Migrations/Version20201208105207.php` migration was added which extends existing adjustments with additional details (context).

   Depending on the type of adjustment, additionally defined information are:

        * Taxation details (percentage and relation to tax rate)
        * Shipping details (shipping relation)
        * Taxation for shipping (combined details of percentage and shipping relation)

   This data is fetched based on two assumptions:

        * Order level taxes relates to shipping only (default Sylius behaviour)
        * Tax rate name has not changed since the time, the first order has been placed

   If these are not true, please adjust migration accordingly to your need. To exclude following migration from execution run following code:

    ```
    bin/console doctrine:migrations:version 'Sylius\Bundle\CoreBundle\Migrations\Version20201208105207' --add
    ```

1. The base of the `Adjustment` class has changed. If you extend your adjustments already (or have them overridden
   by default, because of Sylius-Standard usage), you should base your Adjustment class
   on `Sylius\Component\Core\Model\Adjustment` instead of `Sylius\Component\Order\Model\Adjustment`.

    ```diff
    -       use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
    +       use Sylius\Component\Core\Model\Adjustment as BaseAdjustment;
    ```

### API v2

For changes according to the API v2, please visit [API v2 upgrade file](UPGRADE-API-1.9.md).
