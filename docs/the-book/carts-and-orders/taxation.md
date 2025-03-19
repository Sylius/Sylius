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

# Taxation

Sylius has a flexible taxation system that allows you to apply taxes based on different products, billing zones, and custom tax calculators. Let’s break it down.

## Tax Categories

To handle taxes in Sylius, you need at least one **Tax Category**. Tax Category groups products with the same tax rate.

If all your products are taxed the same, create a simple category like "Taxable Goods." If you have products with different tax rates (like clothing, books, or food), create separate categories for each.

Additionally, you can create **zones** to apply the correct tax rates for customers from different countries.

**How to Create a Tax Category Programmatically**

```php
/** @var TaxCategoryInterface $taxCategory */
$taxCategory = $this->container->get('sylius.factory.tax_category')->createNew();
$taxCategory->setCode('taxable_goods');
$taxCategory->setName('Taxable Goods');
$this->container->get('sylius.repository.tax_category')->add($taxCategory);
```

Now you have a new Tax Category.

#### Setting a Tax Category on a Product Variant

To calculate taxes for products, each **ProductVariant** needs a Tax Category.

```php
/** @var TaxCategoryInterface $taxCategory */
$taxCategory = $this->container->get('sylius.repository.tax_category')->findOneBy(['code' => 'taxable_goods']);

/** @var ProductVariantInterface $variant */
$variant = $this->container->get('sylius.repository.product_variant')->findOneBy(['code' => 'mug']);
$variant->setTaxCategory($taxCategory);
```

***

## Tax Rates

A **Tax Rate** is the percentage applied to the product price. Tax Rates include details like:

* Whether it is included in the product's price
* The applicable zone (country or region)
* The associated tax category
* The calculator used to compute the tax

**Tax Rates and Price Inclusion**

* **Included in Price**: when taxes are included, the final price will include the tax amount.
  * Example: A 23% VAT on a $10 product will result in a final price of $10, with $1.87 in taxes included.
* **Excluded from Price**: when taxes are excluded, the tax is added to the order total at checkout.
  * Example: A 23% VAT on a $10 product will result in a final price of $12.30, with $2.30 in taxes added at checkout.

**How to Create a Tax Rate Programmatically**

Before creating a tax rate, make sure you understand how **zones** work (see the [Zones](../customers/addresses/zones.md) chapter).

```php
/** @var TaxRateInterface $taxRate */
$taxRate = $this->container->get('sylius.factory.tax_rate')->createNew();
$taxRate->setCode('7%');
$taxRate->setName('7%');
$taxRate->setAmount(0.07);
$taxRate->setCalculator('default');

/** Get a Zone from the repository */
$zone = $this->container->get('sylius.repository.zone')->findOneBy(['code' => 'US']);
$taxRate->setZone($zone);

/** Get a Tax Category from the repository */
$taxCategory = $this->container->get('sylius.repository.tax_category')->findOneBy(['code' => 'alcohol']);
$taxRate->setCategory($taxCategory);

$this->container->get('sylius.repository.tax_rate')->add($taxRate);
```

#### Default Tax Zone

When a customer hasn’t provided an address, Sylius uses the **default tax zone** assigned to the channel to estimate taxes. This allows prices to include taxes even when the customer’s location isn’t known.

#### Applying Taxes

Sylius applies taxes during checkout using the **OrderTaxesProcessor**. This processor calculates the correct taxes based on the products in the cart, the customer’s zone, and the applicable tax rates.

### Tax Calculators

Sylius uses **calculators** to compute taxes. The calculator type is determined by the TaxRate assigned to each product. Sylius provides built-in calculators, but you can also create custom ones.

**Built-in Calculators:**

* **DefaultCalculator**: Calculates taxes with rounding.
* **DecimalCalculator**: Calculates taxes without rounding, distributing decimals among items.

**Creating a Custom Tax Calculator**

To create a custom calculator:

1. Implement the `CalculatorInterface`.
2. Register it as a service and tag it with `sylius.tax_calculator`.

Example:

```php
namespace App\TaxCalculator;

use Sylius\Component\Taxation\Calculator\CalculatorInterface;

class CustomCalculator implements CalculatorInterface {
    public function calculate($baseAmount, array $rateDetails): int {
        // Custom calculation logic
    }

    public function getType(): string {
        return 'custom_calculator';
    }
}
```

Register it:

```yaml
services:
    app.tax_calculator.custom_calculator:
        class: App\TaxCalculator\CustomCalculator
        tags:
            - { name: sylius.tax_calculator }
```
