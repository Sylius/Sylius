# UPGRADE FROM `2.2` TO `2.3`

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
