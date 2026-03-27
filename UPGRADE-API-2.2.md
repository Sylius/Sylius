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
