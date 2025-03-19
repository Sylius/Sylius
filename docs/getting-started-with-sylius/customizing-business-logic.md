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

# Customizing Business Logic

Sylius offers extensive customization options, allowing you to tailor your eCommerce store to your specific needs. Let’s walk through an example of how you can customize Sylius by implementing a **custom shipping calculator**.

## Custom Shipping Calculator

A **shipping calculator** in Sylius calculates the shipping cost for an order. This calculation is usually based on the products and some configuration defined by the admin. Sylius provides two default calculators:

* **FlatRateCalculator**: Charges a fixed rate per order.
* **PerUnitRateCalculator**: Charges a rate based on the number of items in the order.

Let’s say you need to charge based on the number of parcels used to pack the order. You can create a custom calculator to achieve this.

### Step 1: Create the Custom Shipping Calculator

Your custom calculator must implement the `CalculatorInterface`. Below is an example of a **ParcelCalculator**, which charges based on the number of parcels.

```php
<?php
# src/ShippingCalculator/ParcelCalculator.php

declare(strict_types=1);

namespace App\ShippingCalculator;

use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class ParcelCalculator implements CalculatorInterface
{
    public function calculate(ShipmentInterface $subject, array $configuration): int
    {
        $parcelSize = $configuration['size'];
        $parcelPrice = $configuration['price'];

        $numberOfPackages = ceil($subject->getUnits()->count() / $parcelSize);

        return (int) ($numberOfPackages * $parcelPrice);
    }

    public function getType(): string
    {
        return 'parcel';
    }
}
```

In this code, we calculate the number of parcels needed by dividing the total product units by the parcel size. The total shipping cost is then the number of parcels multiplied by the price per parcel.

### Step 2: Register the Custom Calculator

To make this calculator available in the admin panel, register it as a service in your `services.yaml`:

```yaml
# config/services.yaml
services:
    app.shipping_calculator.parcel:
        class: App\ShippingCalculator\ParcelCalculator
        tags:
            - { name: sylius.shipping_calculator }
```

This will allow your custom shipping calculator to be selected when configuring shipping methods in the admin panel.

### Step 3: Configure the Shipping Method in the Admin Panel

Now that your calculator is registered, you can create a new **Shipping Method** in the admin panel using your **ParcelCalculator**. When setting up the shipping method, you will need to provide two configuration options:

* **Size**: How many units fit into one parcel.
* **Price**: The cost per parcel.

<div>

<figure><img src="../.gitbook/assets/shipping-calculator.png" alt=""><figcaption><p>Configuration in the Admin Panel</p></figcaption></figure>

 

<figure><img src="../.gitbook/assets/shipping-cost-1.png" alt=""><figcaption><p>Shipping cost for 1 pacel</p></figcaption></figure>

 

<figure><img src="../.gitbook/assets/shipping-cost-2.png" alt=""><figcaption><p>Shipping cost for 4 parcels</p></figcaption></figure>

</div>

## Testing the Custom Logic via API

Once everything is set up, you can test the logic through the API.

### Add an Item to the Cart

Use the following API call to add an item to the cart:

```bash
curl --location --request PATCH 'https://your-shop-url.com/api/v2/shop/orders/CART_TOKEN/items' --header 'Content-Type: application/merge-patch+json' --data-raw '{
    "productVariant": "/api/v2/shop/product-variants/PRODUCT_VARIANT_CODE",
    "quantity": 1
}'
```

This should return a response with the cart, including the `shippingTotal`:

```json
{
    "taxTotal": 0,
    "shippingTotal": 500,  // Shipping cost in cents
    "orderPromotionTotal": 0
}
```

{% hint style="info" %}
The API returns amounts in the smallest currency unit (e.g., cents for USD). So 500 represents $5.00.
{% endhint %}

### Change Item Quantity

You can modify the quantity of the item in the cart using the following API:

```bash
curl --location --request PATCH 'https://your-shop-url.com/api/v2/shop/orders/CART_TOKEN/items/ORDER_ITEM_ID' --header 'Content-Type: application/merge-patch+json' --data-raw '{
    "quantity": 4
}'
```

This should return a response with the updated `shippingTotal`:

```json
{
    "taxTotal": 0,
    "shippingTotal": 1000,  // Updated shipping cost based on the new quantity
    "orderPromotionTotal": 0
}
```

Congratulations! You’ve now added custom business logic to your Sylius store by creating a custom shipping calculator. With this foundation, you can continue customizing your shop to suit your business needs.
