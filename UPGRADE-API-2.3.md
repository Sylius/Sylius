# UPGRADE FROM `2.2` TO `2.3`

## New Endpoints

### Resend Verification Email
A new shop endpoint has been added, allowing customers to request a new account verification email:

**`POST /api/v2/shop/customers/verification-request`**

Request body:

| Field        | Type   | Required | Description                                                                 |
|--------------|--------|----------|-----------------------------------------------------------------------------|
| `email`      | string | Yes      | The customer's email address.                                               |
| `localeCode` | string | No       | Locale for the email (e.g. `en_US`). Defaults to the current channel locale. |

Returns `202 Accepted` on success (regardless of whether the email was sent).

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

## API Platform

1. Sylius now supports API Platform `^5.0` next to `^4.3`. API Platform 5 requires Symfony `^7.4 || ^8.0`,
   so applications running on Symfony 6.4 stay on API Platform 4. For the changes in API Platform itself,
   see the [API Platform 5.0 upgrade guide](https://api-platform.com/docs/core/upgrade-guide/#api-platform-50-breaking-changes).

2. `Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\UriVariablesAwareContextBuilder` now resolves the URI variables
   from the request attributes, as `uri_variables` is no longer available in the serializer context on API Platform 5.

   `Sylius\Bundle\ApiBundle\Serializer\ContextBuilder\AbstractInputContextBuilder::resolveValue()` now receives the current request
   as a third argument. It will be added to the method signature in Sylius 3.0:

   ```php
   abstract protected function resolveValue(array $context, ?array $extractedAttributes/* , ?Request $request = null */): mixed;
   ```

   If you have custom context builders extending `AbstractInputContextBuilder` that read `$context['uri_variables']`,
   add the `?Request $request = null` argument to their `resolveValue()` method and read the route parameters from `$request->attributes` instead.
