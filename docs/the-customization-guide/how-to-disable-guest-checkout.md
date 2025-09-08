# Customizing Checkout

## How to disable guest checkout?

In some projects, you may want to **require customers to log in or register before proceeding to checkout**.

This guide explains how to disable guest checkout in both the **Shop UI** and the **API** in **Sylius 2.0**.

### Disabling Guest Checkout (Shop UI)

To prevent guests from accessing the checkout process via the standard web interface:

### 1.  Update `security.yaml`

Edit the `config/packages/security.yaml` file and add an access control rule to restrict access to the checkout URL:

```yaml
# config/packages/security.yaml

security:
    access_control:
        - { path: "%sylius.security.shop_regex%/checkout", role: ROLE_USER }
```

{% hint style="success" %}
💡 This means that only logged-in users (with at least `ROLE_USER`) can access `/checkout`.
{% endhint %}

### 2. Result

If a guest user tries to start the checkout process, they will be **redirected to the login or registration page**. After successful authentication, they are automatically taken back to the checkout addressing step.

<figure><img src="../.gitbook/assets/image (68).png" alt=""><figcaption></figcaption></figure>

***

### Disabling Guest Checkout (Shop API)

By default, Sylius 2.0's **API** allows anonymous users to complete checkout. To restrict this and **require login for checkout-related API routes**, follow the steps below.

### 1. Allow anonymous users limited access

Update your `security.yaml` to **allow public access only for the following actions**:

* Creating and removing a cart (POST,  `/api/v2/shop/orders`)
* Viewing and updating the cart by token (GET, POST, DELETE `/api/v2/shop/orders/{tokenValue}` and `/api/v2/shop/orders/{tokenValue}/items`)

Add this to your `security.yaml`:

```diff
# config/packages/security.yaml

security:
    access_control:
-       - { path: "%sylius.security.api_shop_regex%/.*", role: PUBLIC_ACCESS }       
+       - { path: "%sylius.security.api_shop_regex%/orders", methods: [POST, GET, DELETE], role: PUBLIC_ACCESS }
+       - { path: "%sylius.security.api_shop_regex%/orders/.*/items", methods: [POST, GET, DELETE], role: PUBLIC_ACCESS }
+       - { path: "%sylius.security.api_shop_regex%/.*", role: ROLE_USER }
```

{% hint style="success" %}
Learn more about Symfony access control [here](https://symfony.com/doc/current/security/access_control.html)!
{% endhint %}

{% hint style="warning" %}
### ⚠️ Important

The last rule (`%sylius.security.api_shop_regex%/.*`) must be **at the bottom** of your access control list to avoid prematurely matching other routes.
{% endhint %}

### 2. Result

#### What This Configuration Allows:

* ✅ Anonymous users can:
  * Create a cart (`POST /api/v2/shop/orders`)
  * View and update cart contents (`GET`, `POST`, `DELETE /api/v2/shop/orders/{tokenValue}/items`)
* ❌ Anonymous users **cannot**:
  * Set addresses `PUT /api/v2/shop/orders/{tokenValue}`
  * Choose shipping methods `PATCH /api/v2/shop/orders/{tokenValue}/shipments/{shipmentId}`
  * Add payments `PATCH /api/v2/shop/orders/{tokenValue}/payments/{paymentId}`
  * Place orders `PATCH /api/v2/shop/orders/{tokenValue}/complete`

If they try, they will receive a `401 Unauthorized` error.

```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

To proceed, the client must authenticate using JWT (typically via /api/v2/shop/customers/token).
