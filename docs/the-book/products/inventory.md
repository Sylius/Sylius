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

# Inventory

Sylius uses a straightforward approach to inventory management. The current stock level of an item is stored on the **ProductVariant** entity, specifically as the `onHand` value.

## InventoryUnit

The **InventoryUnit** represents a physical unit of a product variant available in the shop. It is related to a **Stockable** entity (usually a **ProductVariant**) through the **InventoryUnitInterface**. In Sylius Core, this relationship exists on the **OrderItemUnit**, which implements the **InventoryUnitInterface**.

## Inventory On Hold

Putting items **onHold** reserves stock before the customer has paid for the order. This happens when the checkout process is completed.

{% hint style="info" %}
Items `onHold` are not removed from the `onHand` inventory yet. For example, if there are 5 items `onHand`, and a customer buys 2, after checkout there will still be 5 `onHand` and 2 `onHold`.
{% endhint %}

## Availability Checker

Sylius provides the **AvailabilityChecker** service to help you check if items are available in stock. It has two main methods:

* `isStockAvailable`: Checks if at least one item is available.
* `isStockSufficient`: Checks if a given quantity of items is available.

{% hint style="info" %}
You can also use the following Twig functions to check inventory:`sylius_inventory_is_available & sylius_inventory_is_sufficient`
{% endhint %}

### OrderInventoryOperator

The **OrderInventoryOperator** service manages stock for each **ProductVariant** in an order. It provides the following key methods:

* **hold**: Called when an order’s checkout is completed. It puts inventory units `onHold`, but does not remove them from `onHand`.
* **sell**: Called when an order’s payment status changes to `paid`. This removes items from both `onHold` and `onHand`.
* **release**: Releases items from `onHold`, returning them to `onHand`.
* **giveBack**: Returns sold items back to the `onHand` inventory.
* **cancel**: Cancels an order, whether paid or unpaid. It uses both the `giveBack` and `release` methods to adjust inventory accordingly.

### How does Inventory work on examples?

You can explore all the inventory use cases designed in Sylius through the **Behat scenarios for** [**Inventory**](https://github.com/Sylius/Sylius/tree/2.0/features/admin/inventory). These cover real-world scenarios such as holding, selling, releasing, and returning stock.

\
