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

# ➕ Multi-Source Inventory

<div data-full-width="false"><figure><img src="../../.gitbook/assets/sylius-docs-plusfeature-start (1).png" alt=""><figcaption></figcaption></figure></div>

Sylius Plus offers a more advanced approach to inventory management than the open-source version. While the Community Edition allows only one stock amount per variant, **Sylius Plus Multi-Source Inventory** enables you to create multiple **Inventory Sources** and manage different stock amounts for each source.

As an Admin, you can create multiple **Inventory Sources** (e.g., warehouses, stores, fulfillment centers) and assign them to specific **Channels**. For example, if you have two channels—"DACH" and "France"—and three warehouses in Paris, Berlin, and Vienna, you may want to fulfill orders for the **France** channel only from the Paris warehouse.

For customers, the experience is seamless. They are unaware of the different warehouses; their orders are usually fulfilled. However, you can see which Inventory Source is used to fulfill an order as an Admin. In the future, Sylius will also support splitting shipments from multiple Inventory Sources within a single order.

## Inventory Source

An **Inventory Source** is the location from which a product is shipped, such as a warehouse, fulfillment center, or physical store. Admins can add, modify, and delete Inventory Sources from the admin panel.

<figure><img src="../../.gitbook/assets/inventory_sources_index.png" alt=""><figcaption></figcaption></figure>

Each IS has its inventory management page, where you can manage stock levels of all items in their inventories.

<figure><img src="../../.gitbook/assets/inventory_source_stock_management.png" alt=""><figcaption></figcaption></figure>

To enable product tracking at an Inventory Source, navigate to the **Inventory** tab of the **Product Variant** edit page.&#x20;

<figure><img src="../../.gitbook/assets/product_variant_stock_management.png" alt=""><figcaption></figcaption></figure>

After an order is placed, you will be able to see from which Inventory Source the shipment will be fulfilled.

<figure><img src="../../.gitbook/assets/inventory_source_shipment.png" alt="" width="251"><figcaption></figcaption></figure>

## InventorySourceStock on ProductVariant

Each **Product** and **Product Variant** in Sylius Plus can have stock levels assigned to multiple Inventory Sources. This is similar to how stock is managed in the open-source version, where stock is represented by `onHand` and `onHold` values.

The **inventorySourceStock** behaves like the traditional stock amount in single-source inventory but is tracked separately for each Inventory Source. Stock levels can be managed in the **Inventory** section of the admin panel, where you can view the inventory for each source independently.

You can also disable inventory tracking for specific products if needed.

## Inventory Source resolving

When fulfilling orders, Sylius Plus uses a resolver to determine which Inventory Source will be used. The system applies a set of filters to choose the best Inventory Source for the order.

By default, Sylius Plus provides three filters:

1. **Sufficient** (priority = 0): Selects sources that can fulfill all ordered products.
2. **EnabledChannel** (priority = 8): Selects sources enabled for the current channel.
3. **Priority** (priority = -256): Sorts the filtered sources based on their priority.

The resolver picks the first available Inventory Source based on these filters. If no source can be resolved, an `UnresolvedInventorySource` exception is thrown.

You can add custom filters by implementing the `Sylius\Plus\Inventory\Application\Filter\InventorySourcesFilterInterface` and registering it with the `sylius_plus.inventory.inventory_sources_filter` tag. Filters can be assigned higher or lower priority values depending on your needs.

{% hint style="info" %}
For more details on creating a custom Inventory Sources filter, refer to [this Cookbook](../../the-cookbook-2.0/how-to-create-a-custom-inventory-sources-filter.md).
{% endhint %}

### Resolving InventorySourceStock for ordered products

{% hint style="warning" %}
In Sylius Plus, stock is released after the shipment is **shipped**, not when the order is paid, which is the default behavior in the Community Edition. This allows for more accurate inventory tracking, especially in cases where shipment fulfillment might be delayed.
{% endhint %}

## How does Multi-Source Inventory work on examples?

{% hint style="success" %}
You can explore all use cases for Sylius Plus inventory management by reviewing the **Behat scenarios** included in the Sylius Plus package.
{% endhint %}

## Multi-source inventory fixtures

### **Inventory Sources Fixture**

This fixture creates **Inventory Sources** (initially without products) and enables them for specific channels:

```yaml
hamburg_warehouse:
   code: 'hamburg_warehouse'
   name: 'Hamburg Warehouse'
   priority: 10
   channels:
      - 'HOME_WEB'
      - 'FASHION_WEB'
```

### **Inventory Source Stocks Fixture**

This fixture adds stock for specific Inventory Sources. You can define which taxons and channels to include, and the system will resolve the union of these sets.

```yaml
stocks_in_frankfurt_warehouse:
    inventory_source: 'frankfurt_warehouse'
    products_from:
        taxons_codes:
            - 'caps'
            - 'dresses'
        channels_codes:
            - 'HOME_WEB'
            - 'FASHION_WEB'
```

<div data-full-width="false"><figure><img src="../../.gitbook/assets/sylius-docs-plusfeature-end.png" alt=""><figcaption></figcaption></figure></div>
