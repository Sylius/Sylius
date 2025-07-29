# UPGRADE FROM `2.1` TO `2.2`

### Routing

The routing path for the `sylius_admin_locale_delete` route has been updated:

```diff
- admin/locales/{id}
+ admin/locales/{id}/delete
```

The routing path for the `sylius_admin_product_review_accept` route has been updated:

```diff
- admin/product-review/{id}/accept
+ admin/product-reviews/{id}/accept
```

The routing path for the `sylius_admin_product_review_reject` route has been updated:

```diff
- admin/product-review/{id}/reject
+ admin/product-reviews/{id}/reject
```

The routing path for the `sylius_admin_product_variant_delete` route has been updated:

```diff
- admin/products/{productId}/variants/{id}
+ admin/products/{productId}/variants/{id}/delete
```

The routing path for the `sylius_admin_promotion_coupon_delete` route has been updated:

```diff
- /admin/promotions/{promotionId}/coupons/{id}
+ /admin/promotions/{promotionId}/coupons/{id}/delete
```


The routing path for the `sylius_admin_promotion_coupon_bulk_delete` route has been updated:

```diff
- /admin/promotions/{promotionId}/coupons/bulk-delete
+ /admin/promotions/{promotionId}/coupons/bulk_delete
```
