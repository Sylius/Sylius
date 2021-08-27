# UPGRADE FROM `v1.9.5` TO `v1.9.6`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.9.3` TO `v1.9.4`

1. `Sylius\Bundle\ApiBundle\DataProvider\OrderCollectionDataProvider` has been removed and the same logic
   is now implemented in `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\OrdersByLoggedInUserExtension`

1. The service `Sylius\Bundle\ApiBundle\Serializer\ProductVariantSerializer` has been changed to `Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer`
   and its first argument `NormalizerInterface $objectNormalizer` has been removed from constructor.

# UPGRADE FROM `v1.9.2` TO `v1.9.3`

1. The endpoint `GET api/v2/order-items/{id}/adjustments` has been changed to `GET api/v2/admin/order-items/{id}/adjustments`

# UPGRADE FROM `v1.8.X` TO `v1.9.0`

1. `/new-api` prefix has been changed to `/api/v2`. Please adjust your routes accordingly.
   Admin API is hardcoded to `/api/v1` instead of `/api/v{version}`.

### New API

1. Adjust your `config/packages/security.yaml`.

    * Parameters from `config/packages/security.yaml` has been moved to separated bundles.
      You may delete them if you are using the default values:

        ```diff
        - parameters:
        -     sylius.security.admin_regex: "^/%sylius_admin.path_name%"
        -     sylius.security.api_regex: "^/api/v1"
        -     sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|api/.*|api$|media/.*)[^/]++"
        -     sylius.security.new_api_route: "/api/v2"
        -     sylius.security.new_api_regex: "^%sylius.security.new_api_route%"
        -     sylius.security.new_api_admin_route: "%sylius.security.new_api_route%/admin"
        -     sylius.security.new_api_admin_regex: "^%sylius.security.new_api_admin_route%"
        -     sylius.security.new_api_shop_route: "%sylius.security.new_api_route%/shop"
        -     sylius.security.new_api_shop_regex: "^%sylius.security.new_api_shop_route%"
        ```

    * If you are not using the default values, you may need to add and change parameters:

        ```diff
            parameters:
        -       sylius.security.api_regex: "^/api"
        -       sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|new-api|api/.*|api$|media/.*)[^/]++"
        -       sylius.security.new_api_route: "/new-api"
        +       sylius.security.api_regex: "^/api/v1"
        +       sylius.security.shop_regex: "^/(?!%sylius_admin.path_name%|api/.*|api$|media/.*)[^/]++"
        +       sylius.security.new_api_route: "/api/v2"
        +       sylius.security.new_api_user_account_route: "%sylius.security.new_api_shop_route%/account"
        +       sylius.security.new_api_user_account_regex: "^%sylius.security.new_api_user_account_route%"
        ```

    * Add new access control configuration and reorder it:

        ```diff
            security:
                access_control:
        +           - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
        -           - { path: "%sylius.security.new_api_route%/admin/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_admin_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_user_account_regex%/.*", role: ROLE_USER }
        -           - { path: "%sylius.security.new_api_route%/shop/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        +           - { path: "%sylius.security.new_api_shop_route%/authentication-token", role: IS_AUTHENTICATED_ANONYMOUSLY }
        -           - { path: "%sylius.security.new_api_admin_regex%/.*", role: ROLE_API_ACCESS }
                    - { path: "%sylius.security.new_api_shop_regex%/.*", role: IS_AUTHENTICATED_ANONYMOUSLY }
        ```

1. Unified API registration path in shop has been changed from `/new-api/shop/register` to `/new-api/shop/customers/`.

1. Identifier needed to retrieve a product in shop API endpoint (`/new-api/shop/products/{id}`) has been changed from `slug` to `code`.

1. `config/packages/fos_rest.yaml` rules have been changed to:

    ```diff
        rules:
    -       - { path: '^/api/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    +       - { path: '^/api/v1/.*', priorities: ['json', 'xml'], fallback_format: json, prefer_extension: true }
    ```

1. To have better control over the serialization process, we introduced `shop` and `admin` prefixes
   to names of serialization groups on `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/*` and `src/Sylius/Bundle/ApiBundle/Resources/config/serialization/*`.
   Several additional serialization groups have been rephrased, to improve readability and predictability of them.
   If you are using they on your custom entity `api_resource` configuration or serialization groups, you should check
   if one of these changes may affect on your app. If yes, change all occurs by this pattern:

- created serialization groups for `Locale` resource as: `admin:locale:read` and `admin:locale:create`
- `adjustment:read` changed to: `admin:adjustment:read` and `shop:adjustment:read`
- `admin_user:create` changed to: `admin:admin_user:create`
- `admin_user:read` changed to: `admin:admin_user:read`
- `admin_user:update` changed to: `admin:admin_user:update`
- `avatar_image:read` changed to: `admin:avatar_image:read`
- `cart:add_item` changed to: `shop:cart:add_item`
- `cart:address` changed to: `shop:cart:address`
- `cart:apply_coupon` changed to: `shop:cart:apply_coupon`
- `cart:change_quantity` changed to: `shop:cart:change_quantity`
- `cart:complete` changed to: `shop:cart:complete`
- `cart:remove_item` changed to: `shop:cart:remove_item`
- `cart:select_payment_method` changed to: `shop:cart:select_payment_method`
- `cart:select_shipping_method` changed to: `shop:cart:select_shipping_method`
- `cart:update` changed to: `shop:cart:update`
- `channel:create` changed to: `admin:channel:create`
- `channel:read` changed to: `admin:channel:read`
- `checkout:read` changed to: `shop:cart:read`
- `country:read` changed to: `admin:country:read`
- `currency:read` changed to: `admin:currency:read`
- `customer:password:write` changed to: `shop:customer:password:update`
- `customer:read` changed to: `admin:customer:read` and `shop:customer:read`
- `customer:update` changed to: `shop:customer:update`
- `customer_group:create` changed to: `admin:customer_group:create`
- `customer_group:read` changed to: `admin:customer_group:read`
- `customer_group:update` changed to: `admin:customer_group:update`
- `exchange_rate:create` changed to: `admin:exchange_rate:create`
- `exchange_rate:read` changed to: `admin:exchange_rate:read`
- `exchange_rate:update` changed to: `admin:exchange_rate:update`
- `order:create` changed to: `shop:order:create`
- `order:read` changed to: `admin:order:read`
- `order:update` changed to: `admin:order:update`
- `order_item:read` changed to: `admin:order_item:read` and `shop:order_item:read`
- `order_item_unit:read` changed to: `admin:order_item_unit:read` and `shop:order_item_unit:read`
- `payment:read` changed to: `admin:payment:read` and `shop:payment:read`
- `payment_method:read` changed to: `admin:payment_method:read` and `shop:payment_method:read`
- `product:create` changed to: `admin:product:create`
- `product:read` changed to: `admin:product:read` and `shop:product:read`
- `product:update` changed to: `admin:product:update`
- `province:read` changed to: `admin:province:read`
- `province:update` changed to: `admin:province:update`
- `shipment:read` changed to: `admin:shipment:read` and `shop:shipment:read`
- `shipment:update` changed to: `admin:shipment:update`
- `shipping_category:create` changed to: `admin:shipping_category:create`
- `shipping_category:read` changed to: `admin:shipping_category:read`
- `shipping_category:update` changed to: `admin:shipping_category:update`
- `shipping_method:create` changed to: `admin:shipping_method:create`
- `shipping_method:read` changed to: `admin:shipping_method:read`
- `shipping_method:update` changed to: `admin:shipping_method:update`
- `shop:currencies:read` changed to: `shop:currency:read`
- `shop:customer:write` changed to: `shop:customer:create`
- `shop_billing_data:read` changed to: `admin:shop_billing_data:read`
- `tax_category:read` changed to: `admin:tax_category:read`
- `tax_category:update` changed to: `admin:tax_category:update`
- `tax_category:create` changed to: `admin:tax_category:create`
- `taxon:read` changed to: `admin:taxon:read` and `shop:taxon:read`
- `taxon:update` changed to: `admin:taxon:update`
- `taxon:create` changed to: `admin:taxon:create`
- `zone:read` changed to: `admin:zone:read`
- `zone:update` changed to: `admin:zone:update`
- `zone:create` changed to: `admin:zone:create`
- `zone_member:read` changed to: `admin:zone_member:read`
- removed redundant `zone_member:write`
