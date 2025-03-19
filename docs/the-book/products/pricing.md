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

# Pricing

In Sylius, the pricing system handles how product prices are managed across different channels. Each channel can have unique pricing configurations, allowing for flexibility in multi-currency and multi-channel setups.

{% hint style="info" %}
All prices in Sylius are saved in the **base currency** of each channel separately.
{% endhint %}

### Price and Original Price

* **Price**: This is the current price of a product variant, displayed in the catalog. It can be modified by catalog promotions or other pricing strategies.
* **Original Price**: The original price is the product's price before any discounts or promotions. This value is often displayed as crossed-out in the catalog, emphasizing the discounted price. If no original price is defined, Sylius will automatically copy the current price to the original price field for comparison.

**Example**: If a product’s original price is set at $100 and a promotion applies a 20% discount, the current price will be $80, and the original price ($100) will appear crossed out in the catalog.

### Minimum Price

* **Minimum Price**: This is the lowest price a product can be reduced to, preventing promotions from dropping the price below a set threshold. It works in both **Catalog Promotions** (affecting catalog display prices) and **Cart Promotions** (affecting prices during checkout).

If a product qualifies for multiple promotions but one of them drops the price below the minimum, that promotion will only reduce the price to the minimum level. Any further promotions will not apply.

**Example**: If the minimum price for a product is set at $50, and the price after two promotions is $55, the third promotion will reduce the price to exactly $50, and no more. Further promotions will not be applied.

### Currency per Channel

Sylius operates on the concept of **Channels**, and each channel has its own base currency. All prices for products within a channel are saved in this base currency, ensuring consistency across the channel.

When working with channel-specific data (e.g., product prices, fixed discounts from promotions), always consider the currency tied to that channel.

### Exchange Rates

For each currency defined in Sylius, you can set up **Exchange Rates**. These are used to convert prices from the base currency of a channel into other currencies, providing an estimated price for users viewing the catalog in a currency different from the channel’s base currency.

Exchange Rates ensure that users from different regions can see an approximation of the product's price in their local currency, though all pricing calculations remain based on the channel's base currency.

**Example**: If a channel’s base currency is EUR, but a user views the store in USD, the exchange rate set between EUR and USD will be used to calculate and display the approximate price in USD.
