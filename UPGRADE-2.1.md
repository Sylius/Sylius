# UPGRADE FROM `2.1.13` TO `2.1.14`

### Admin resource translations

Translations for admin-managed resources (such as product options, attributes, associations) are now resolved using the current admin locale instead of the default `%locale%` parameter.

Previously, translation-aware query builders relied on a static `%locale%` parameter, which could lead to inconsistencies when the admin user was working in a different language than the application default.

This has been updated to dynamically resolve the locale from the admin context.

### Before
```yaml
sylius_grid:
    grids:
        sylius_admin_product_association_type:
            driver:
                name: doctrine/orm
                options:
                    class: "%sylius.model.product_association_type.class%"
                    repository:
                        method: createListQueryBuilder
                        arguments: ["%locale%"]
```

### After
```yaml
sylius_grid:
    grids:
        sylius_admin_product_association_type:
            driver:
                name: doctrine/orm
                options:
                    class: "%sylius.model.product_association_type.class%"
                    repository:
                        method: createListQueryBuilder
                        arguments: ["expr:service('sylius.context.locale').getLocaleCode()"]
```

### Restore missing admin resource show title

A missing page title has been restored to the admin resource show pages (e.g. product, order, customer, shipment, catalog promotion).

Added separated show routes for each resource with dedicated show page templates.

### Order payment method eligibility check

The `Sylius\Bundle\CoreBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator` and 
`Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator` now checks also if 
payment method is assigned to the channel of the order.

## Shop API

1. The `Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\ChannelAndLocaleBasedExtension` service (`sylius_api.doctrine.orm.query_extension.shop.product.channel_and_locale_based`)
   now implements `QueryItemExtensionInterface` in addition to `QueryCollectionExtensionInterface`.
   The service is tagged with `api_platform.doctrine.orm.query_extension.item`.

If you decorate this service, make sure your decorator also implements
`ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface` and proxies the `applyToItem` method.

# UPGRADE FROM `2.1.12` TO `2.1.13`

### Telemetry improvements

Telemetry has been improved with per-query database timeouts to prevent slow queries from blocking admin panel
requests. Timeouts are applied automatically using platform-specific mechanisms (MySQL, MariaDB, PostgreSQL).

The global query timeout (in milliseconds, default: 60000, minimum: 1000) is **configurable** via environment variable:

```dotenv
SYLIUS_TELEMETRY_QUERY_TIMEOUT=30000
```

Additionally, telemetry collection is now rate-limited — it will be skipped if it was already triggered within
the last hour, preventing redundant data collection on rapid admin page loads.

# UPGRADE FROM `2.1.11` TO `2.1.12`

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

### [Admin] XSS in taxon tree and autocompletes (High)

A stored XSS vulnerability has been fixed in the admin panel's taxon tree and autocomplete components. Taxon names
containing malicious HTML/JavaScript were rendered without escaping.

**Affected files:**

- `Sylius\Bundle\AdminBundle\Resources\assets\controllers\ProductTaxonTreeController.js` — taxon name is now escaped
  via `textContent` before insertion into the DOM template.
- `Sylius\Bundle\AdminBundle\Resources\assets\controllers\TaxonTreeController.js` — uses `textContent` assignment
  instead of string interpolation with `replaceAll('__TAXON_NAME__', name)`.
- New file `Sylius\Bundle\AdminBundle\Resources\assets\scripts\autocomplete-xss-protection.js` — intercepts
  `autocomplete:pre-connect` events and escapes label fields before rendering.

**No action required** — this fix is applied automatically upon updating. If you have custom JavaScript that renders
taxon names or autocomplete labels using `innerHTML` or string templates, review your code for similar XSS vectors.

### [Shop] XSS in taxon breadcrumbs (High)

A stored XSS vulnerability has been fixed in the shop breadcrumbs template. Breadcrumb labels were rendered using
Twig's `|raw` filter, allowing injected HTML in taxon or order names to execute in the browser.

**Changes in `shared/breadcrumbs.html.twig`:**

```diff
- <a class="link-reset" href="{{ item.path }}">{{ item.label|raw }}</a>
+ <a class="link-reset" href="{{ item.path }}">{{ item.label }}</a>
  ...
- <span class="text-body-tertiary text-break">{{ item.label|raw }}</span>
+ <span class="text-body-tertiary text-break">{{ item.label }}</span>
```

If you have overridden the `shared/breadcrumbs.html.twig` template, ensure you are not using the `|raw` filter
on user-controllable label values.

### [Shop] XSS in ApiLoginController (Medium)

A DOM-based XSS vulnerability has been fixed in `ApiLoginController.js`. The server error message was inserted
using `innerHTML`, allowing malicious content in the response to execute JavaScript.

```diff
- errorElement.innerHTML = response.message;
+ errorElement.textContent = response.message;
```

**No action required** — this fix is applied automatically upon updating.

### [Shop] IDOR in checkout address LiveComponent (High)

An Insecure Direct Object Reference (IDOR) vulnerability has been fixed in the checkout address LiveComponent.
The `addressFieldUpdated()` method in `Checkout\Address\FormComponent` used `$this->addressRepository->find($addressId)`,
allowing any authenticated customer to load another customer's address by manipulating the `#[LiveArg]` value.

The fix replaces `find()` with `findOneByCustomer()` to scope the lookup to the current customer:

```diff
- $address = $this->addressRepository->find($addressId);
+ $customer = $this->customerContext->getCustomer();
+ if (!$customer instanceof CustomerInterface) {
+     return;
+ }
+ $address = $this->addressRepository->findOneByCustomer((string) $addressId, $customer);
+ if (null === $address) {
+     return;
+ }
```

Additionally, `SummaryComponent::refreshCart()` and `WidgetComponent::refreshCart()` no longer accept an external
`$cartId` argument — they use the internally held cart reference instead, preventing cart ID manipulation.

**No action required** — this fix is applied automatically upon updating. If you have extended or overridden
`Checkout\Address\FormComponent`, `Cart\SummaryComponent`, or `Cart\WidgetComponent`, review your customizations
to ensure they do not expose similar IDOR vectors.

### [API] Cart authorization bypass (High)

An authorization bypass has been fixed in `AddItemToCartHandler`. Previously, any authenticated user could add items
to another user's cart by providing a different cart token in the API request.

The handler now validates that the current user has access to the cart before modifying it:

```diff
 final readonly class AddItemToCartHandler
 {
     public function __construct(
         // ...
+        private ?UserContextInterface $userContext = null,
     ) {
     }

     public function __invoke(AddItemToCart $addItemToCart): OrderInterface
     {
         // ...
+        $this->assertCartAccessible($cart);
         // ...
     }
+
+    private function assertCartAccessible(OrderInterface $cart): void
+    {
+        // Validates current user matches cart owner
+        // Guest carts remain accessible
+        // Throws NotFoundHttpException if access denied
+    }
 }
```

The `UserContextInterface` service is now injected into the handler via `command_handlers.xml`.

**No action required** — this fix is applied automatically upon updating. If you have overridden the
`AddItemToCartHandler` or its service definition, ensure the new `UserContextInterface` argument is included.

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

# UPGRADE FROM `2.1.8` TO `2.1.9`

## Telemetry

Sylius 2.1.9 introduces anonymous telemetry to help us understand how Sylius is used and improve the platform.

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

**Database migration (optional):**

This release includes an optional database migration that adds an index to improve telemetry query performance.
The telemetry system works without this index, but adding it will make data collection faster, especially for stores with large order volumes.

To run the migration:

```bash
php bin/console doctrine:migrations:migrate
```

If you want to skip this migration:

```bash
php bin/console doctrine:migrations:version 'Sylius\Bundle\CoreBundle\Migrations\Version20251126120000' --add --no-interaction

# For PostgreSQL:
php bin/console doctrine:migrations:version 'Sylius\Bundle\CoreBundle\Migrations\Version20251126120001' --add --no-interaction
```

For more details, see the [Telemetry documentation](https://docs.sylius.com/the-book/configuration/telemetry).

## Autocomplete Form Types

1. The `choice_value => 'code'` option has been removed from autocomplete form types (`ProductAutocompleteType`,
   `ProductVariantAutocompleteType`, `ProductAttributeAutocompleteType`, `TaxonAutocompleteType`,
   `ProductOptionAutocompleteType`) to fix performance issue with large datasets (#17953).
   Autocomplete fields now use entity IDs instead of codes as their internal values.

## Dashboard

1. The `Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\StatisticsComponent` constructor now requires
   a `Symfony\Component\Clock\ClockInterface` as the third argument. Not passing it is deprecated
   and will be prohibited in Sylius 3.0.

1. The `$clock` argument in `Sylius\Bundle\AdminBundle\Notification\HubNotificationProvider` constructor is deprecated
   and will be removed in Sylius 3.0.

1. The priorities of hookables in `sylius_admin.order.update.content.form.shipping_address` hook have been adjusted
   to match `sylius_admin.order.update.content.form.billing_address` and fix duplicate priority values.
   If you have customized these hooks or added custom hookables with priorities between the old values,
   please review your priority configuration:

   | Hookable       | Old Priority | New Priority |
   |----------------|--------------|--------------|
   | company        | 700          | 800          |
   | first_name     | 600          | 700          |
   | last_name      | 500          | 600          |
   | country        | 400          | 500          |
   | phone_number   | 300          | 400          |
   | street_address | 200          | 300          |

# UPGRADE FROM `2.1.7` TO `2.1.8`

## State Machine

1. Product Review Average Rating Update
   The `after` callback for updating average ratings when a review is accepted has been disabled to prevent duplicate calculations.
   This change affects the `sylius_product_review` state machine configuration in the `accept` transition.

The average rating updater callback had priority `-100` and was being executed twice, which has now been fixed by disabling this specific callback.

```diff
        callbacks:
            after:
                sylius_update_rating:
                    on: ["accept"]
                    do: ["@sylius.updater.product_review.average_rating", "updateFromReview"]
                    args: ["object"]
                    priority: -100
+                   disabled: true
```
# UPGRADE FROM `2.1.5` TO `2.1.6`

### API Platform

1. API Platform 4.2: the title and description fields are no longer present (use hydra:title / hydra:description and violations instead).
   PHPUnit and Behat tests have been updated to account for these changes.

# UPGRADE FROM `2.1.2` TO `2.1.3`

### Deprecations

1. Not passing the `Symfony\Component\Routing\Matcher\UrlMatcherInterface` instance as the last argument to the `Sylius\Bundle\ShopBundle\Locale\StorageBasedLocaleSwitcher` constructor is deprecated and will be required in Sylius 3.0.

### Service configuration changes

1. The priority of the `sylius_shop.context.locale.storage_based` service's tag `sylius.context.locale` has been changed from `-64` to `64` to ensure proper execution order in the locale context chain.

# UPGRADE FROM `2.0` TO `2.1`

### General

1. The `sylius_admin_customer_orders_statistics` route has been deprecated.

1. The minimum version of Symfony 7 packages has been bumped from Symfony `^7.1` to `^7.2`

1. The `tabler` package has been updated to version `^1.3.0`. Please pay attention to the accordion element in final applications, as its implementation has changed.

### Twig Hooks

1. The `sylius_admin.dashboard.index.content.latest_statistics.new_customers` hook has been deprecated and disabled.
   It has been replaced by the `sylius_admin.dashboard.index.content.latest_statistics.pending_actions`.

1. The `history`, `cancel` and `resend_confirmation_email` hookables from `'sylius_admin.order.show.content.header.title_block.actions'` 
   hook have been deprecated and disabled. Now these templates are located in `'sylius_admin.order.show.content.header.title_block.actions.list'` hook.

1. `'sylius_shop.account.address_book.index.content.main.buttons'` hook has been deprecated and disabled. Content 
   of this hook has been moved to `'sylius_shop.account.address_book.index.content.main.header'` section.

1. `'sylius_shop.account.address_book.index.content.main.buttons.add_address'` hook has been deprecated and disabled. 
   Content of this hook has been moved to `'sylius_shop.account.address_book.index.content.main.header.buttons.add_address'` section.

1. The `price`, `original_price`, `minimum_price` hookables from `'sylius_admin.product.update.content.form.sections.channel_pricing'`
   hook have been deprecated and disabled. Now these templates are located in `'sylius_admin.product.create.content.form.sections.channel_pricing.info'`.

### Assets

#### Overview of Changes
Sylius has modernized its asset management system with these key improvements:
- Bundle-prefixed controller paths
- JSON-based configuration
- Flexible controller registration options

#### Updated Controller Paths
All core controllers now use standardized bundle prefixes:

**Admin Controllers**  

| Old Path                         | New Path                                              |
|----------------------------------|-------------------------------------------------------|
| `slug`                           | `@sylius/admin-bundle/slug`                           |
| `taxon-slug`                     | `@sylius/admin-bundle/taxon-slug`                     |
| `taxon-tree`                     | `@sylius/admin-bundle/taxon-tree`                     |
| `delete-taxon`                   | `@sylius/admin-bundle/delete-taxon`                   |
| `product-attribute-autocomplete` | `@sylius/admin-bundle/product-attribute-autocomplete` |
| `product-taxon-tree`             | `@sylius/admin-bundle/product-taxon-tree`             |
| `save-positions`                 | `@sylius/admin-bundle/save-positions`                 |
| `compound-form-errors`           | `@sylius/admin-bundle/compound-form-errors`           |
| `tabs-errors`                    | `@sylius/admin-bundle/tabs-errors`                    |

**Shop Controller**  
`api-login` → `@sylius/shop-bundle/api-login`

#### New Configuration System

**Configuration Files**
```text
assets/
  admin/
    controllers.json  # Admin controller configurations
  shop/
    controllers.json  # Shop controller configurations
  controllers.json # Shared controllers configurations imported via flex
```

**Example Configuration**:
```json
{
  "@sylius/admin-bundle/slug": {
    "enabled": true,
    "fetch": "lazy"
  }
}
```

**Key Actions**:
- Enable/disable controllers
- Change loading behavior (lazy/eager)
- Extend with custom controllers

#### Controller Registration Methods

1. **Automatic Discovery**
    - Files in `./controllers/` directory
    - Naming pattern: `[name]_controller.js`
    - Auto-registered with Stimulus

2. **JSON Configuration**
    - Pre-configured bundle controllers
    - Managed via `controllers.json` files
    - Lazy-loaded by default

3. **Manual Registration**
   ```javascript
   // In bootstrap.js:
   import CustomController from './custom_controller_dir/custom_controller';
   app.register('custom', CustomController);
   ```
   Use for:
    - Third-party controllers
    - Non-standard locations
    - Advanced initialization

#### Migration Guide
1. Update all controller references to use new prefixed paths
2. Review [Sylius-Standard PR #1126](https://github.com/Sylius/Sylius-Standard/pull/1126) for implementation details

**Note**: The old mechanism will remain functional until you actively migrate to the new system.
