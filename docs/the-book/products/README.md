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

## Product Units

### How to Sell Products in Various Units and Bulk Quantities?

To sell products in various units and bulk quantities using **Sylius**, you’ll leverage **Product Options** and **Product Option Values**. These features allow you to define different configurations of a product, such as size, packaging, and bulk quantities, enabling you to offer a flexible shopping experience for both retail and B2B customers.

#### Key Sylius Concepts:

1. **Product Options**: This is where you define how a product can vary, also for packaging type or quantity. For example, you might have a **Packaging** option that allows customers to choose between different sizes and packs.
2.  **Product Option Values**: These represent the specific choices under each option. Each **Product Option** (like **Packaging**) will have multiple **Option Values** that describe the different forms in which the product is available. For example, under **Packaging**, the option values would be:

    * **330ML Can**
    * **1.5L Bottle**
    * **Pack of 6 Bottles (6x1.5L)**
    * **Pallet of 84 Packs (6x1.5L)**

    <figure><img src="../../.gitbook/assets/image (63).png" alt=""><figcaption><p>Example with options matching</p></figcaption></figure>
3. **Product Variants**: These are the actual instances of a product that a customer can purchase, and each variant is associated with specific **Option Values**. For example, a **Coke 330ML Can** variant would be assigned the **330ML Can** option value under the **Packaging** option.

<figure><img src="../../.gitbook/assets/image (62).png" alt=""><figcaption><p>Example with variants matching configuration configuration</p></figcaption></figure>

4. **Variant Selection Method:** You can control how variants are shown to the customer.

* Go to **Catalog → Products → Edit**
* Scroll to **Variant selection method**
* Choose one of:
  * Variant choice
  * Options Matching

<figure><img src="../../.gitbook/assets/image (64).png" alt=""><figcaption></figcaption></figure>

#### Steps to Implement:

* **Create Product Options**: Define the **Packaging** option with values like **330ML Can**, **1.5L Bottle**, **Pack of 6 Bottles**, and **Pallet of 84 Packs**. You can also define a **Bulk Quantity** option if needed, with values like **10,000 Cans** or **50,000 Cans** for larger orders.
* **Assign Option Values**: For each Product Option, add the corresponding values that represent the various configurations of your product (e.g., **1.5L Bottle** or **Pallet of 84 Packs**).
* **Create Product Variants**: Once your options are set, create **Variants** for each combination of unit or bulk purchase type, ensuring that each variant has the correct pricing, stock, and relevant option values.

This setup allows you to offer your product in various **sizes**, **packaging types**, and **bulk quantities**, catering to both individual customers (for smaller quantities) and businesses (for large-scale orders).

By structuring your product setup this way, you ensure that customers can easily select the unit size or quantity that best fits their needs while keeping inventory management straightforward.
