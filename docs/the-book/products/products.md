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

# Products

The **Product** model in Sylius represents unique items in your store. Every product can have different **variants** (e.g., size, color) and **attributes** (e.g., material, brand). Each product must have at least one variant to be sold in the store.

## How to create a Product?

Before we learn how to create products that can be sold, let’s see how to create a product without its complex dependencies.

```php
/** @var ProductFactoryInterface $productFactory **/
$productFactory = $this->get('sylius.factory.product');

/** @var ProductInterface $product */
$product = $productFactory->createNew();
```

Creating an empty product is not enough to save it in the database. It needs to have a `name`, a `code` and a `slug`.

```php
$product->setName('T-Shirt');
$product->setCode('00001');
$product->setSlug('t-shirt');

/** @var RepositoryInterface $productRepository */
$productRepository = $this->get('sylius.repository.product');

$productRepository->add($product);
```

{% hint style="warning" %}
Although the product is now added to the system, it cannot yet be purchased by customers because it lacks variants.
{% endhint %}

## Variants

A **ProductVariant** represents a unique version of a product (e.g., a T-shirt in size Medium). Variants can have their own pricing configurations, inventory tracking, and more.

* You can create variants based on **product options** (e.g., size, color).
* You can also create variants without using options, giving you flexibility in how you manage product versions.



### Virtual Product Variants, that do not require shipping

If a product does not require shipping (e.g., a digital download or software), you can set the `shippingRequired` property to `false` on its **ProductVariant.**

### How to create a Product with a Variant?

If you need to sell products in different forms (e.g., hardcover vs. paperback), you can create a product with variants as follows:

1. Create the base product as shown previously.
2. Create a variant using the **ProductVariantFactory**.

```php
/** @var ProductVariantFactoryInterface $productVariantFactory **/
$productVariantFactory = $this->get('sylius.factory.product_variant');

/** @var ProductVariantInterface $productVariant */
$productVariant = $productVariantFactory->createNew();
```

3. Set the necessary attributes for the variant:

```php
$productVariant->setName('Hardcover');
$productVariant->setCode('1001');
$productVariant->setPosition(1);
$productVariant->setProduct($product);
```

4. Finally, save the variant to the database:

```php
/** @var RepositoryInterface $productVariantRepository */
$productVariantRepository = $this->get('sylius.repository.product_variant');

$productVariantRepository->add($productVariant);
```

## Options

When managing products with different variations (e.g., T-shirts in various sizes and colors), you’ll need to define **ProductOptions**. Each option can have multiple **ProductOptionValues**.

**Example Options**

* **Size**: S, M, L, XL, XXL
* **Color**: Red, Green, Blue

After defining the options, Sylius can automatically generate product variants based on the possible combinations.

### How to create a Product with Options and Variants?

Here’s how to set up a product with options (e.g., color) and automatically generate variants for it.

```php
/** @var ProductOptionInterface $option */
$option = $this->get('sylius.factory.product_option')->createNew();
$option->setCode('t_shirt_color');
$option->setName('T-Shirt Color');

// Prepare an array with values for your option, with codes, locale code and option values.
$valuesData = [
    'OV1' => ['locale' => 'en_US', 'value' => 'Red'],
    'OV2' => ['locale' => 'en_US', 'value' => 'Blue'],
    'OV3' => ['locale' => 'en_US', 'value' => 'Green'],
];

foreach ($valuesData as $code => $values) {
    /** @var ProductOptionValueInterface $optionValue */
    $optionValue = $this->get('sylius.factory.product_option_value')->createNew();

    $optionValue->setCode($code);
    $optionValue->setFallbackLocale($values['locale']);
    $optionValue->setCurrentLocale($values['locale']);
    $optionValue->setValue($values['value']);

    $option->addValue($optionValue);
}
```

After you have an Option created and you keep it as `$option` variable let’s add it to the Product and generate **Variants**.

```php
// Assuming that you have a basic product let's add the previously created option to it.
$product->addOption($option);

// Having option of a product you can generate variants. Sylius has a service for that operation.
/** @var ProductVariantGeneratorInterface $variantGenerator */
$variantGenerator = $this->get('sylius.generator.product_variant');

$variantGenerator->generate($product);

// And finally add the product, with its newly generated variants to the repository.
/** @var RepositoryInterface $productRepository */
$productRepository = $this->get('sylius.repository.product');

$productRepository->add($product);
```
