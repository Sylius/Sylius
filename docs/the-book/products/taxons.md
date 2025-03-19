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

# Taxons

In Sylius, **Taxons** work similarly to categories in other eCommerce systems. They allow you to organize and categorize your products in a highly flexible way, which is essential for modern eCommerce. The Taxon system is hierarchical, meaning you can create parent categories with nested subcategories.

#### Example of a Category Tree:

```
Category
 |
 |\__ Clothes
 |         \_ T-Shirts
 |          \_ Shirts
 |           \_ Dresses
 |            \_ Shoes
 |
 \__ Books
         \_ Fantasy
          \_ Romance
           \_ Adventure
            \_ Other

Gender
 |
 \_ Male
  \_ Female
```

## How to create a Taxon?

As always with Sylius resources, to create a new object you need a factory.&#x20;

#### Creating a Single (Non-Nested) Taxon:

```php
/** @var FactoryInterface $taxonFactory */
$taxonFactory = $this->get('sylius.factory.taxon');

/** @var TaxonInterface $taxon */
$taxon = $taxonFactory->createNew();

$taxon->setCode('category');
$taxon->setName('Category');
```

**Creating a Nested Taxon (Category Tree):**

To create a tree of categories, first create the parent taxon. Then, create child taxons and add them as children to the parent.

```php
/** @var TaxonInterface $childTaxon */
$childTaxon = $taxonFactory->createNew();

$childTaxon->setCode('clothes');
$childTaxon->setName('Clothes');

$taxon->addChild($childTaxon);
```

**Saving the Taxon:**

Once the parent taxon is added to the system, all its child taxons will be saved automatically.

```php
/** @var TaxonRepositoryInterface $taxonRepository */
$taxonRepository = $this->get('sylius.repository.taxon');

$taxonRepository->add($taxon);
```

## How to assign a Taxon to a Product?

To categorize your products, you need to assign them to taxons using the `addProductTaxon()` method.

**Example: Assigning a Taxon to a Product**

```php
/** @var ProductInterface $product */
$product = $this->container->get('sylius.factory.product')->createNew();
$product->setCode('product_test');
$product->setName('Test');

/** @var TaxonInterface $taxon */
$taxon = $this->container->get('sylius.factory.taxon')->createNew();
$taxon->setCode('food');
$taxon->setName('Food');

/** @var RepositoryInterface $taxonRepository */
$taxonRepository = $this->container->get('sylius.repository.taxon');
$taxonRepository->add($taxon);


/** @var ProductTaxonInterface $productTaxon */
$productTaxon = $this->container->get('sylius.factory.product_taxon')->createNew();
$productTaxon->setTaxon($taxon);
$productTaxon->setProduct($product);

$product->addProductTaxon($productTaxon);

/** @var EntityManagerInterface $productManager */
$productManager = $this->container->get('sylius.manager.product');

$productManager->persist($product);
$productManager->flush();
```

## What is the mainTaxon of a Product?

The **mainTaxon** field in the product entity is used to designate the primary taxon for a product. It is especially useful for generating breadcrumbs or for custom logic like link generation.

To set the **mainTaxon** on a product, use the `setMainTaxon()` method.
