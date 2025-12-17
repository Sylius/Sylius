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
