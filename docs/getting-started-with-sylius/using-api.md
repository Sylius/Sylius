---
layout:
  title:
    visible: true
  description:
    visible: false
  tableOfContents:
    visible: true
  outline:
    visible: true
  pagination:
    visible: true
---

# Using API

Since Sylius 1.8, we have offered a new API based on ApiPlatform. Below are examples of how to use the API for basic shop operations. **Public API documentation is available** [**here**](https://master-ce.demo.sylius.com/api/v2/docs)**.**

## Register a customer

To register a new customer, send a single `POST` request:

```bash
curl -X 'POST' \
    'https://master-ce.demo.sylius.com/api/v2/shop/customers' \
    -H 'accept: */*' \
    -H 'Content-Type: application/ld+json' \
    -d '{
        "firstName": "shop",
        "lastName": "user",
        "email": "shop.user@example.com",
        "password": "pa$$word",
        "subscribedToNewsletter": true
    }'
```

If the response status is **204**, the customer was registered successfully.

<figure><img src="../.gitbook/assets/api_platform_shop_customer_post.png" alt=""><figcaption></figcaption></figure>

### Login to the shop

After registering a customer, you can log in to obtain an authentication token, which is required to access more shop endpoints.

```bash
curl -X 'POST' \
    'https://master-ce.demo.sylius.com/api/v2/shop/customers/token' \
    -H 'accept: application/json' \
    -H 'Content-Type: application/json' \
    -d '{
        "email": "shop.user@example.com",
        "password": "pa$$word"
    }'
```

If successful, the response will have a **200** status code and include the token and customer IRI:

```json
{
    "token": "string",
    "customer": "iri"
}
```

{% hint style="warning" %}
If your shop requires email authentication, no token will be returned.
{% endhint %}

Use the token to authenticate subsequent API requests:

```bash
curl -X 'METHOD' \
    'api-url' \
    -H 'accept: application/ld+json' \
    -H 'Authorization: Bearer token'
```

## Basic Operations: Products, Carts, and Orders

Once the customer is authorized, you can start interacting with products, carts, and orders via the API. Below are the typical operations:

### Adding product to cart

**Create a Cart:**

You can create a cart for a logged-in customer by sending a `POST` request:

```bash
curl -X 'POST' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders' \
  -H 'accept: application/ld+json' \
  -H 'Content-Type: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -d '{
        # "localeCode": "string" (optional)
  }'
```

{% hint style="info" %}
```
You can have your cart in a different locale if needed. If no `localeCode` is provided, the channel's default will be added automatically.
```
{% endhint %}

Response status **201** will include cart details and the cart's `tokenValue`, which is needed for subsequent operations.

<figure><img src="../.gitbook/assets/api_platform_shop_orders_post.png" alt=""><figcaption></figcaption></figure>

**Add a Product to the Cart:**

First, retrieve a product variant by sending a `GET` request:

```bash
curl -X 'GET' \
  'https://master-ce.demo.sylius.com/api/v2/shop/product-variants?page=1&itemsPerPage=30' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token'
```

```javascript
// ...
{
  "@id": "/api/v2/shop/product-variants/Everyday_white_basic_T_Shirt-variant-0",
  "@type": "ProductVariant",
  "id": 123889,
  "code": "Everyday_white_basic_T_Shirt-variant-0",
  "product": "/api/v2/shop/products/Everyday_white_basic_T_Shirt",
  "optionValues": [
    "/api/v2/shop/product-option-values/t_shirt_size_s"
  ],
  "translations": {
    "en_US": {
      "@id": "/api/v2/shop/product-variant-translations/123889",
      "@type": "ProductVariantTranslation",
      "id": 123889,
      "name": "S",
      "locale": "en_US"
    }
  },
  "price": 6420,
  "originalPrice": 6420,
  "inStock": true
}
// ...
```

Use the `@id` of the desired variant, and add it to the cart:

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/rl1KwtiSLA/items' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "productVariant": "/api/v2/shop/product-variants/Everyday_white_basic_T_Shirt-variant-0",
    "quantity": 1
  }'
```

<figure><img src="../.gitbook/assets/api_platform_shop_orders_items_patch.png" alt=""><figcaption></figcaption></figure>

The response status **200** confirms the product has been added to the cart.

```bash
{
  # Rest of orders body
  "items": [
    {
      "@id": "/api/v2/shop/order-items/59782",
      "@type": "OrderItem",
      "variant": "/api/v2/shop/product-variants/Everyday_white_basic_T_Shirt-variant-0",
      "productName": "Everyday white basic T-Shirt",
      "id": 59782,
      "quantity": 1,
      "unitPrice": 6420,
      "total": 6869,
      "subtotal": 6420
    }
  ],
  # Rest of orders body
}
```

### Changing the Product Quantity in the Cart

To change the quantity of a product already added to the cart, use the following `PATCH` request:

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/OPzFiAWefi/items/59782' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "quantity": 3
  }'
```

<figure><img src="../.gitbook/assets/api_platform_shop_orders_change_quantity.png" alt=""><figcaption></figcaption></figure>

The response status **200** confirms the quantity change.

<figure><img src="../.gitbook/assets/api_platform_shop_orders_change_quantity_response.png" alt=""><figcaption></figcaption></figure>

### Completing the Order

Once the cart is filled, follow these steps to complete the order:

#### **1. Add Customer Address**

Add the customer's billing and shipping address by sending a `PATCH` request:

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/rl1KwtiSLA/address' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "email": "shop.user@example.com",
    "billingAddress": {
        "city": "California",
        "street": "Coral str",
        "postcode": "90210",
        "countryCode": "US",
        "firstName": "David",
        "lastName": "Copperfield"
      }
  }'
```

<figure><img src="../.gitbook/assets/api_platform_shop_orders_addressing.png" alt=""><figcaption></figcaption></figure>

{% hint style="info" %}
If no `shippingAddress` is provided, the `billingAddress` will be used for both.
{% endhint %}

<figure><img src="../.gitbook/assets/api_platform_shop_orders_addressing_response.png" alt=""><figcaption></figcaption></figure>

#### **2. Select Shipping and Payment Methods**

First, get the available shipping and payment methods:

```bash
curl -X 'GET' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/rl1KwtiSLA' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token'
```

Use the methods' `@id` in the next steps.

```bash
"payments": [
    {
      "@id": "/api/v2/shop/payments/20446",
      "@type": "Payment",
      "id": 20446,
      "method": "/api/v2/shop/payment-methods/cash_on_delivery"
    }
],
"shipments": [
    {
      "@id": "/api/v2/shop/shipments/17768",
      "@type": "Shipment",
      "id": 17768,
      "method": "/api/v2/shop/shipping-methods/ups"
    }
],
```

**For Shipping:**

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/rl1KwtiSLA/shipments/17768' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "shippingMethod": "/api/v2/shop/shipping-methods/ups"
  }'
```

<figure><img src="../.gitbook/assets/api_platform_shop_orders_choose_shipping.png" alt=""><figcaption></figcaption></figure>

**For Payment:**

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/{cartToken}/payments/{paymentId}' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "paymentMethod": "/api/v2/shop/payment-methods/cash_on_delivery"
  }'
```

#### **3. Complete the Order**

Finally, complete the order by sending the following request:

```bash
curl -X 'PATCH' \
  'https://master-ce.demo.sylius.com/api/v2/shop/orders/rl1KwtiSLA/complete' \
  -H 'accept: application/ld+json' \
  -H 'Authorization: Bearer token' \
  -H 'Content-Type: application/merge-patch+json' \
  -d '{
    "notes": "your note"
  }'
```

<figure><img src="../.gitbook/assets/api_platform_shop_orders_completed.png" alt=""><figcaption></figcaption></figure>

The response status **200** confirms the order completion, with the `checkoutState` changed to **completed**.

```bash
{
    # Orders body
    "currencyCode": "USD",
    "localeCode": "en_US",
    "checkoutState": "completed",
    "paymentState": "awaiting_payment",
    "shippingState": "ready",
    # Orders body
}
```

#### Final Output

The full checkout process has now been completed using Sylius API. With this API, you can create a fully functional shop frontend based on Sylius' backend logic.
