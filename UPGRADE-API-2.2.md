# UPGRADE FROM `2.2.6` TO `2.2.7`

## Shop API

1. The `Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\ChannelAndLocaleBasedExtension` service (`sylius_api.doctrine.orm.query_extension.shop.product.channel_and_locale_based`)
   now implements `QueryItemExtensionInterface` in addition to `QueryCollectionExtensionInterface`.
   The service is tagged with `api_platform.doctrine.orm.query_extension.item`.

If you decorate this service, make sure your decorator also implements
`ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface` and proxies the `applyToItem` method.

# UPGRADE FROM `2.1` TO `2.2`

## Modified routes

1. The routes for payment requests resource have been renamed to follow the shop API naming convention.

| Old route                              | New route                              |
|----------------------------------------|----------------------------------------|
| `sylius_api_show_payment_request_get`  | `sylius_api_shop_payment_request_get`  |
| `sylius_api_show_payment_request_post` | `sylius_api_shop_payment_request_post` |
| `sylius_api_show_payment_request_put`  | `sylius_api_shop_payment_request_put`  |

## HTTP Status Code Changes

### Missing Required Fields Validation

The HTTP status code for missing required fields in API requests has been changed from `400 Bad Request` to `422 Unprocessable Content` to follow REST API best practices and RFC 9110 semantics.

Additionally, the redundant `code` field has been removed from the error response body, as the status code is already available in the HTTP response headers.

**Before:**
```json
{
    "@context": "/api/v2/contexts/Error",
    "@type": "hydra:Error",
    "status": 400,
    "detail": "Request does not have the following required fields specified: email."
}
```

**After:**
```json
{
    "@context": "/api/v2/contexts/Error",
    "@type": "hydra:Error",
    "status": 422,
    "detail": "Request does not have the following required fields specified: email."
}
```

**Breaking changes:**
1. HTTP status code changed: `400` → `422`
2. Response body field `code` removed (was redundant with HTTP status header)

**Affected endpoints:** All POST/PATCH endpoints that validate required fields (e.g., `/api/v2/shop/customers`, `/api/v2/shop/orders/{token}/items`, etc.)

**Migration guide:**
- If your API client checks `response.status === 400` for missing fields, change it to `response.status === 422`
- If your API client reads `response.data.code`, use `response.status` (HTTP header) instead

**References:** RFC 9110 (400 = syntactic errors, 422 = semantic/validation errors)


## API Platform resource classes as container parameters

The following API Platform resource classes are now defined as container parameters, allowing you to override them:

| Parameter | Default class |
|---|---|
| `sylius_api.command.account.reset_password.class` | `Sylius\Bundle\ApiBundle\Command\Account\ResetPassword` |
| `sylius_api.command.account.verify_shop_user.class` | `Sylius\Bundle\ApiBundle\Command\Account\VerifyShopUser` |
| `sylius_api.command.admin.account.reset_password.class` | `Sylius\Bundle\ApiBundle\Command\Admin\Account\ResetPassword` |
| `sylius_api.command.send_contact_request.class` | `Sylius\Bundle\ApiBundle\Command\SendContactRequest` |

To override a class, define the parameter in your configuration:

```yaml
parameters:
    sylius_api.command.account.reset_password.class: App\Api\Command\CustomResetPassword
```
