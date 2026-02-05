# UPGRADE FROM `2.2` TO `2.3`

## Routing

A few route paths have been updated:

| Name                                      | Path (before)                                         | Path (after)                                          |
|-------------------------------------------|-------------------------------------------------------|-------------------------------------------------------|
| sylius_admin_locale_delete                | `/admin/locales/{id}`                                 | `/admin/locales/{id}/delete`                          |
| sylius_admin_product_review_accept        | `/admin/product-review/{id}/accept`                   | `/admin/product-reviews/{id}/accept`                  |
| sylius_admin_product_review_reject        | `/admin/product-review/{id}/reject`                   | `/admin/product-reviews/{id}/reject`                  |
| sylius_admin_product_variant_delete       | `/admin/products/{productId}/variants/{id}`           | `/admin/products/{productId}/variants/{id}/delete`    |
| sylius_admin_promotion_coupon_delete      | `/admin/promotions/{promotionId}/coupons/{id}`        | `/admin/promotions/{promotionId}/coupons/{id}/delete` |
| sylius_admin_promotion_coupon_bulk_delete | `/admin/promotions/{promotionId}/coupons/bulk-delete` | `/admin/promotions/{promotionId}/coupons/bulk_delete` |
| sylius_admin_shop_user_delete             | `/admin/shop-user/{id}`                               | `/admin/shop-users/{id}/delete`                       |
| sylius_shop_account_address_book_delete   | `/{_locale}/account/address-book/{id}`                | `/{_locale}/account/address-book/{id}/delete`         |
