# UPGRADE FROM `2.2.8` TO `2.2.9`

### JWTs are now bound to the API section they have been issued for

Tokens are stamped with an `aud` claim (`sylius-api-admin` or `sylius-api-shop`) and a
`principal_type` claim, and they are rejected by the other API section. This closes the case where
an administrator and a shop customer share an e-mail address, in which a shop token was accepted by
the admin API and resolved to that administrator.

The expectations are keyed by firewall name, `api_admin` and `api_shop` by default, and are
configured under `sylius_api.jwt.firewall_expectations`. If your application renamed either firewall, or serves
the API through an additional JWT firewall (e.g. a marketplace vendor API), add an entry for it, otherwise the
tokens of that firewall are rejected:

```yaml
sylius_api:
    jwt:
        firewall_expectations:
            api_admin:
                audience: sylius-api-admin
                principal: Sylius\Component\Core\Model\AdminUserInterface
            api_shop:
                audience: sylius-api-shop 
                principal: Sylius\Component\Core\Model\ShopUserInterface
            api_custom:
                audience: sylius-api-custom
                principal: App\Entity\CustomUserInterface
```

This map is replaced, not merged, when configured — keep the built-in `api_admin`/`api_shop`
entries alongside your own. Configuring an entry here only affects JWT validation; the referenced firewall must
still be defined in `security.yaml`, and the `principal` must be an existing class or interface implemented by
the corresponding user.

### Payment request actions requested from shop context are now restricted to an allowlist

The shop `POST /api/v2/shop/orders/{tokenValue}/payment-requests` operation accepted any `action` value the caller
supplied and announced it on the payment request command bus, with order ownership as the only authorization. Where a
gateway wires a refund-class action into its action-indexed command provider, a customer could request `refund` on
their own paid order and have it executed at the payment service provider, while Sylius kept the order as `paid`.

The requested action is now validated against an allowlist before the payment method is looked up and before any
gateway command provider is consulted; everything else is rejected with `422 Unprocessable Entity`. The default
allowlist is `capture`, `authorize`, `status` and `notify`, so `refund`, `cancel`, `payout` and `sync` are refused
from shop context.

If your gateway exposes a different shop-facing action, add it to the allowlist, otherwise shop-context calls
requesting it will be rejected:

```yaml
sylius_api:
    shop_payment_request:
        allowed_actions: ['capture', 'authorize', 'status', 'notify']
```

# UPGRADE FROM `2.2.7` TO `2.2.8`

## Shop API

1. Payment requests can now only be created or updated for **placed orders** (orders whose checkout
   state is `completed`). Creating or updating a payment request for an order that has not completed
   checkout now fails validation.

   The eligibility rule is enforced by the new
   `Sylius\Bundle\ApiBundle\Validator\Constraints\OrderPaymentRequestEligibility` constraint applied to
   the `AddPaymentRequest` and `UpdatePaymentRequest` commands, and is delegated to the new
   `Sylius\Bundle\ApiBundle\Checker\OrderPaymentRequestEligibilityChecker` service
   (`sylius_api.checker.order_payment_request_eligibility`). If your application requires different
   rules, you can override or decorate this service to adjust the behaviour.

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
