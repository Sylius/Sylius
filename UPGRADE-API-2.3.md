# UPGRADE FROM `2.2` TO `2.3`

## Admin API

1. The `Administrator` resource exposes two new boolean properties, `administrationAccess` and `apiAccess`, which
   represent the admin user access levels backed by the `ROLE_ADMINISTRATION_ACCESS` and `ROLE_API_ACCESS` roles.

   They are readable in the `sylius:admin:admin_user:index` and `sylius:admin:admin_user:show` serialization groups,
   and writable in the `sylius:admin:admin_user:create` and `sylius:admin:admin_user:update` ones. Both are therefore
   present in the responses of all `Administrator` operations:

   ```diff
    {
        "@context": "/api/v2/contexts/Administrator",
        "@id": "/api/v2/admin/administrators/1",
        "@type": "Administrator",
        "firstName": "John",
        "lastName": "Doe",
        "localeCode": "en_US",
        "avatar": null,
   +    "administrationAccess": true,
   +    "apiAccess": false,
        "id": 1,
        "username": "john.doe",
        "email": "john.doe@example.com",
        "enabled": true
    }
   ```

   If your tests compare full `Administrator` payloads, update the expected responses accordingly.

## Order

1. The Order resource (admin and shop) now exposes a new `orderAndItemPromotionTotal` property, next to the
   existing `orderPromotionTotal`.

   `orderPromotionTotal` sums the unit-level, item-level and order-level promotion adjustments together.
   The unit-level part is already reflected in `itemsSubtotal`/each item's `subtotal`, so combining
   `itemsSubtotal` with `orderPromotionTotal` double-counts that part. `orderAndItemPromotionTotal` sums
   only the item-level and order-level adjustments, so it can safely be added next to `itemsSubtotal`
   without double-counting.

   The property is available in the `sylius:admin:order:index`, `sylius:admin:order:show`,
   `sylius:shop:cart:show` and `sylius:shop:order:account:show` serialization groups.
