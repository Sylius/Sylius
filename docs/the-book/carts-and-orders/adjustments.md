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

# Adjustments

**Adjustments** are closely tied to orders in Sylius. They influence the total amount of an order and can be applied at different levels:

* **Order Level**
* **OrderItem Level**
* **OrderItemUnit Level**

### Types of Adjustments

Adjustments can be divided into three main groups:

**Promotion Adjustments**

Applied when promotions or discounts are used

* Example: `Order Promotion Adjustments`, `OrderItem Promotion Adjustments`

**Shipping Adjustments**

Applied to the cost of shipping

* Example: `Shipping Adjustments`, `Shipping Promotion Adjustments`

**Tax Adjustments**

Applied to calculate taxes on orders

* Example: `Tax Adjustments`

#### Positive vs Negative Adjustments

|                              Positive Adjustments                             |                              Negative Adjustments                              |
| :---------------------------------------------------------------------------: | :----------------------------------------------------------------------------: |
| These are charges that increase the total amount (e.g., shipping fees, taxes) | These are discounts that reduce the total amount (e.g., promotional discounts) |

### Creating an Adjustment Programmatically

Adjustments need to be linked to an order to make sense. Here’s how you can create one:

1.  **Get the Adjustment Factory**

    First, you’ll need to get the adjustment factory and create a new adjustment instance.

    ```php
    /** @var AdjustmentInterface $adjustment */
    $adjustment = $this->container->get('sylius.factory.adjustment')->createNew();
    ```
2.  **Set the Adjustment Type and Amount**

    * Set the **type** of the adjustment (available types are found in `AdjustmentInterface`).
    * Provide the **amount** (in the base currency).
    * Optionally, set whether the adjustment is **neutral** (neutral adjustments don’t affect the total like taxes already included in the price).

    ```php
    $adjustment->setType(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
    $adjustment->setAmount(200); // Amount in base currency
    $adjustment->setNeutral(false); // Affects the total
    $adjustment->setLabel('Test Promotion Adjustment');
    ```
3.  **Add the Adjustment to an Order**

    After setting up the adjustment, add it to the relevant order:

    ```php
    $order->addAdjustment($adjustment);
    ```
4.  **Save the Changes**

    To apply the changes, update the order in the database:

    ```php
    $this->container->get('sylius.manager.order')->flush();
    ```

#### Adding Adjustments to Order Items or Item Units

If you want to add adjustments at the **OrderItem** level, make sure the adjustment is attached to the **OrderItem**. If it’s for an **OrderItemUnit**, apply it at the **OrderItemUnit** level.

{% hint style="info" %}
Adjustments on different levels affect only that specific part of the order.
{% endhint %}

#### Locking an Adjustment

You can lock an adjustment to prevent it from being removed during recalculations. This is useful for scenarios like expired promotions that still need to be applied to the order.

```php
$adjustment->lock();
```

For example, if a promotion is no longer applicable but you still want it to be applied (e.g., an expired coupon), locking the adjustment ensures it remains active.
