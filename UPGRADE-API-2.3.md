# UPGRADE FROM `2.2` TO `2.3`

## New Endpoints

### Resend Verification Email ([#19002](https://github.com/Sylius/Sylius/pull/19002))

A new shop endpoint has been added, allowing customers to request a new account verification email:

**`POST /api/v2/shop/customers/verification-request`**

Request body:

| Field        | Type   | Required | Description                                                                 |
|--------------|--------|----------|-----------------------------------------------------------------------------|
| `email`      | string | Yes      | The customer's email address.                                               |
| `localeCode` | string | No       | Locale for the email (e.g. `en_US`). Defaults to the current channel locale. |

Returns `202 Accepted` on success (regardless of whether the email was sent).

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
