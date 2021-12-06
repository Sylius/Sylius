# UPGRADE FROM `v1.10.0` TO `v1.10.1`

1. API is disabled by default, to enable it you need to set flag to ``true`` in ``config/packages/_sylius.yaml``:

    ```yaml
    sylius_api:
        enabled: true
    ```

# UPGRADE FROM `v1.9.X` TO `v1.10.0`

1. API CartShippingMethod key `cost` has been changed to `price`.

1. API Ship Shipment endpoint (PATCH api/v2/admin/shipments/{id}/ship) body value `tracking` has been changed to `trackingCode`.

1. To have better control over the serialization process, we introduced `shop` and `admin` prefixes to names of serialization groups on `src/Sylius/Bundle/ApiBundle/Resources/config/api_resources/*` and `src/Sylius/Bundle/ApiBundle/Resources/config/serialization/*`.
   Several additional serialization groups have been rephrased, to improve readability and predictability of them.
   If you are using they on your custom entity `api_resource` configuration or serialization groups, you should check if one of these changes may affect on your app. If yes, change all occurs by this pattern:

- `product_review:update` changed to: `admin:product_review:update` and `shop:product_review:update`

- `product_association_type` changed to: `admin:product_association_type`

- `product_option` changed to: `admin:product_option`

- `product_option_value` changed to: `admin:product_option_value`

- `product_taxon` changed to: `admin:product_taxon` and `shop:product_taxon`

- `product_variant` changed to: `admin:product_variant` and `shop:product_variant`

- `taxon_translation` changed to: `admin:taxon_translation` and `shop:taxon_translation`

1. We've removed `Sylius\Bundle\ApiBundle\ApiPlatform\Bridge\Doctrine\ORM\SubresourceDataProvider`. It's no longer needed because `ApiPlatform\Core\Bridge\Doctrine\Orm\SubresourceDataProvider` has the same logic.

1. API Change Quantity endpoint `PATCH api/v2/admin/orders/{tokenValue}/change-quantity` has been changed to `PATCH api/v2/admin/orders/{tokenValue}/items/{orderItemId}` and its body value `orderItemId` has been removed (now it is a route parameter) and `newQuantity` has been renamed to `quantity`.

1. API Add to cart endpoint `PATCH /api/v2/shop/orders/{tokenValue}/items` no longer requires `productCode` in request body.

1. Channel pricing resource and its serialization has been removed from shop section

1. `Sylius\Bundle\ApiBundle\DataProvider\AddressCollectionDataProvider` has been removed in favour of `Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension\AddressesExtension`

1. Second argument of `Sylius\Bundle\ApiBundle\DataPersister\AddressDataPersister` has been changed
   from `CustomerContextInterface $customerContext` to `UserContextInterface $userContext`

#### Commands

1. We've removed `productCode` from `Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart` command.

1. Endpoints with changed code to IRI:

PATCH on `/api/v2/shop/account/orders/{tokenValue}/payments/{paymentId}`:

````
{
    - "paymentMethodCode": "string"
    + "paymentMethod": "string"
}
````

POST on `/api/v2/shop/product-reviews`:

````
{
      "title": "string",
      "rating": 0,
      "comment": "string",
    - "productCode": "string",
    + "product": "string",
      "email": "string"
}
````

POST on `/api/v2/shop/reset-password-requests`:

````
{
    - "localeCode": "string"
    + "locale": "string"
}
````


POST on `api/v2/shop/account-verification-requests`:

````
{
    - "localeCode": "string"
    + "locale": "string"
}
````

PATCH on `/api/v2/shop/account/orders/{tokenValue}/shipments/{shipmentId}`:

````
{
    - "shippingMethodCode": "string"
    + "shippingMethod": "string"
}
````

PATCH on `/api/v2/shop/account/orders/{tokenValue}/items`:

````
{
    - "productVariantCode": "string"
    + "productVariant": "string"
}
````
