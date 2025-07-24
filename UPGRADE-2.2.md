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
