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

# Coupons

Coupons are tightly integrated with Sylius Cart Promotions, allowing promotions to be activated by unique codes. Here’s how to create, apply, and generate coupons for promotions.

### Coupon Parameters

Each coupon has the following attributes:

* **Code**: The unique identifier for the coupon.
* **Expiration Date**: The date when the coupon expires.
* **Usage Limit**: The maximum number of times it can be used.
* **Usage Count**: Tracks how many times the coupon has been used.

## Creating a Coupon-Based Promotion

To create a coupon-based promotion, follow these steps:

1.  **Create a Promotion**

    Begin by creating a new promotion and setting it as **coupon-based**. Only coupon-based promotions can hold multiple coupons.

    ```php
    /** @var PromotionInterface $promotion */
    $promotion = $this->container->get('sylius.factory.promotion')->createNew();

    $promotion->setCode('free_shipping');
    $promotion->setName('Free Shipping');

    // Set the promotion's channel
    $promotion->addChannel($this->container->get('sylius.repository.channel')->findOneBy(['code' => 'US_Web_Store']));

    $promotion->setCouponBased(true);
    ```
2.  **Create a Coupon and Link It to the Promotion**

    Next, create a coupon and associate it with your promotion:

    ```php
    /** @var CouponInterface $coupon */
    $coupon = $this->container->get('sylius.factory.promotion_coupon')->createNew();

    $coupon->setCode('FREESHIPPING');

    $promotion->addCoupon($coupon);
    ```
3.  **Add a Promotion Action**

    Define what action the promotion should take when applied. For a free shipping promotion, create a **100% shipping discount** action:

    ```php
    /** @var PromotionActionFactoryInterface $actionFactory */
    $actionFactory = $this->container->get('sylius.factory.promotion_action');

    // Use a float for percentage discounts (1 = 100%, 0.1 = 10%)
    $action = $actionFactory->createShippingPercentageDiscount(1);

    $promotion->addAction($action);

    // Save the promotion to the repository
    $this->container->get('sylius.repository.promotion')->add($promotion);
    ```

### Applying a Coupon to an Order

To apply a promotion coupon we've just created to an order:

1. Ensure the order has shipments (as the above coupon applies a promotion on shipping).
2. Set the promotion coupon on the order (this simulates a customer applying the coupon code at checkout).
3. Process the order with the **OrderProcessor** to apply the promotion.

```php
$order->setPromotionCoupon($coupon);
$this->container->get('sylius.order_processing.order_processor')->process($order);
```

## Generating Multiple Coupons

For larger promotions, manually creating codes is tedious. Sylius offers the **CouponGenerator** service to automatically generate coupon codes in bulk.

1.  **Retrieve the Promotion**

    First, find the promotion to which you want to add coupons.

    ```php
    $promotion = $this->container->get('sylius.repository.promotion')->findOneBy(['code' => 'simple_promotion']);
    ```
2.  **Configure the Coupon Generator**

    Use `PromotionCouponGeneratorInstruction` to specify the number of coupons, code length, expiration date, and usage limit.

    ```php
    /** @var CouponGeneratorInterface $generator */
    $generator = $this->container->get('sylius.promotion_coupon_generator');

    /** @var PromotionCouponGeneratorInstructionInterface $instruction */
    $instruction = new PromotionCouponGeneratorInstruction();

    $instruction->setAmount(10); // Generate 10 coupons
    $instruction->setPrefix('NEW_YEAR_'); // Optional prefix
    $instruction->setSuffix('_SALE'); // Optional suffix

    $generator->generate($promotion, $instruction);
    ```

    This example generates 10 coupons with the prefix `NEW_YEAR_` and suffix `_SALE` for the `simple_promotion` promotion.
