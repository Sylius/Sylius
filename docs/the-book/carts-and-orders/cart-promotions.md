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

# Cart Promotions

The Cart Promotions system in Sylius is highly flexible, combining **promotion rules** and **actions** to create tailored discount programs.

## Promotion Basics

Each cart promotion has:

* A **unique code** and **name**.
* A **usage limit**: the total number of times it can be used.
* A **validity period**: the time frame during which it is active.
* An **exclusivity option**: exclusive promotions prevent other promotions from being applied.
* A **priority**: used to control the order of application, especially for exclusive promotions.

{% hint style="info" %}
Promotion priorities are assigned as numbers. Higher numbers indicate higher priority, so a promotion with a priority `3` will be applied before one with a priority `1`\
\
**Example of Priority Use**: Suppose you have two promotions—one for 10% off the entire order and another for a $5 discount. In this case, you might want to apply the 10% discount first to maximize its impact.
{% endhint %}

## Creating a Promotion Programmatically

To create a promotion, use the promotion factory:

```php
/** @var PromotionInterface $promotion */
$promotion = $this->container->get('sylius.factory.promotion')->createNew();
$promotion->setCode('simple_promotion_1');
$promotion->setName('Simple Promotion');
```

However, a basic promotion needs **Rules** and **Actions** to be functional.

### Promotion Rules

Promotion rules define the conditions that must be met for a promotion to apply. Each rule has a specific **RuleChecker** that checks order details, such as:

* Whether a specific taxon is in the order.
* If the total price of items in a specific taxon meets a threshold.
* Whether the order total reaches a specified amount.
* and more...

**Default Promotion Rule Types in Sylius**

<table data-header-hidden><thead><tr><th width="196"></th><th></th></tr></thead><tbody><tr><td><strong>Cart Quantity</strong></td><td>Checks if the cart has a certain number of items</td></tr><tr><td><strong>Item Total</strong></td><td>Checks if the cart’s item total meets a specified amount</td></tr><tr><td><strong>Has at least one from taxons</strong></td><td>Ensures the cart includes items from certain taxons</td></tr><tr><td><strong>Total price of items from taxon</strong></td><td>Ensures items from a specific taxon meet a total price threshold</td></tr><tr><td><strong>Nth Order</strong></td><td>Checks if the customer is placing their nth order</td></tr><tr><td><strong>Shipping Country</strong></td><td>Verifies the shipping country matches a specific requirement</td></tr><tr><td><strong>Customer Group</strong></td><td>Ensures the customer belongs to a certain group</td></tr><tr><td><strong>Contains Product</strong></td><td>Checks if a specified product is in the order</td></tr></tbody></table>

**Creating a Promotion Rule Programmatically**

Use the `PromotionRuleFactory` to create and configure rules.

**Example**: To set a rule that requires at least 5 items in the cart:

```php
/** @var PromotionRuleFactoryInterface $ruleFactory */
$ruleFactory = $this->container->get('sylius.factory.promotion_rule');
$quantityRule = $ruleFactory->createCartQuantity('5');
$promotion->addRule($quantityRule);
```

{% hint style="info" %}
Rules define eligibility conditions only. To apply discounts, **actions** are required.
{% endhint %}

### Promotion Actions

Promotion actions define what happens when promotion rules are met. Actions include various types of discounts:

* **Fixed Order Discount**: e.g., $5 off the order total.
* **Percentage Order Discount**: e.g., 10% off the entire order.
* **Fixed Unit Discount**: e.g., $1 off each unit.
* **Percentage Unit Discount**: e.g., 10% off each unit.
* **Shipping Percentage Discount**: e.g., 10% off shipping costs.

{% hint style="info" %}
Actions affect all items in the order by default. To apply discounts to specific items only, use **filters**.
{% endhint %}

**Creating a Promotion Action Programmatically**

Use the `PromotionActionFactory` to create actions. For example, to set a fixed discount of $10:

```php
/** @var PromotionActionFactoryInterface $actionFactory */
$actionFactory = $this->container->get('sylius.factory.promotion_action');
$action = $actionFactory->createFixedDiscount(10);
$promotion->addAction($action);
```

After configuring rules and actions, save the promotion to the repository:

```php
$this->container->get('sylius.repository.promotion')->add($promotion);
```

## Applying Cart Promotions

The **PromotionProcessor** manages the application of cart promotions:

1. Reverts any existing promotions on the order.
2. Checks the eligibility of all available promotions for the order.
3. Applies eligible promotions.

### **Applying a Promotion Manually**

To apply a 10% discount programmatically:

```php
/** @var PromotionInterface $promotion */
$promotion = $this->container->get('sylius.factory.promotion')->createNew();
$promotion->setCode('discount_10%');
$promotion->setName('10% discount');

/** @var PromotionActionFactoryInterface $actionFactory */
$actionFactory = $this->container->get('sylius.factory.promotion_action');
$action = $actionFactory->createPercentageDiscount(10);

$promotion->addAction($action);
$this->container->get('sylius.repository.promotion')->add($promotion);

// Apply the promotion to an order
$this->container->get('sylius.promotion_applicator')->apply($order, $promotion);
```

## Promotion Filters

**Filters** allow actions to target specific groups of products. For example, use a [TaxonFilter](https://github.com/Sylius/Sylius/blob/2.0/src/Sylius/Component/Core/Promotion/Filter/TaxonFilter.php) to apply a promotion only to items within a specific taxon.

{% hint style="info" %}
Check [these scenarios](https://github.com/Sylius/Sylius/blob/2.0/features/promotion/receiving\_discount/receiving\_fixed\_discount\_on\_products\_from\_specific\_taxon.feature) on promotion filters to have a better understanding of them.
{% endhint %}

