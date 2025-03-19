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

# Attributes

Attributes in Sylius allow you to define shared traits among entities. For example, products within the same category may share common attributes, such as a book's **number of pages**, the **brand** of a T-shirt, or any other relevant detail.

## Attribute

The **Attribute** model has a translatable name (for instance `Book pages`), code (`book_pages`) and type (`integer`). There are a few available types of an Attribute:

| Type           | Example Use Case                         |
| -------------- | ---------------------------------------- |
| Text (default) | Brand of a T-shirt                       |
| Checkbox       | Whether a T-shirt is made of cotton      |
| Integer        | Number of items in a set                 |
| Percent        | Cotton percentage in clothing            |
| Textarea       | Detailed product description             |
| Date           | Movie release date                       |
| Datetime       | Event date and time                      |
| Select         | Book genre (multiple selection possible) |

### Non-translatable attribute

Some attributes (dates, author name) don’t need a different value in each locale. For those attributes, we introduced the possibility of disabling translation. Shop Owner declares values only once and regardless of the chosen locale customer will see a proper attribute value.

{% hint style="danger" %}
Once the attribute has disabled translatability it will erase attribute values in all locales for this attribute.
{% endhint %}

## How to create an Attribute?

To give you a better insight into Attributes, let’s have a look at how to prepare and add an Attribute with a Product to the system programmatically.

To assign Attributes to Products firstly you will need a factory for ProductAttributes. The AttributeFactory has a special method createTyped($type), where $type is a string.

The Attribute needs a `code` and a `name` before it can be saved in the repository.

```php
/** @var AttributeFactoryInterface $attributeFactory */
$attributeFactory = $this->container->get('sylius.factory.product_attribute');

/** @var AttributeInterface $attribute */
$attribute = $attributeFactory->createTyped('text');

$attribute->setName('Book cover');
$attribute->setCode('book_cover');

$this->container->get('sylius.repository.product_attribute')->add($attribute);
```

In order to assign value to your Attribute you will need a factory of ProductAttributeValues, use it to create a new value object.

```php
/** @var FactoryInterface $attributeValueFactory */
$attributeValueFactory = $this->container->get('sylius.factory.product_attribute_value');

/** @var AttributeValueInterface $hardcover */
$hardcover = $attributeValueFactory->createNew();
```

Attach the new AttributeValue to your Attribute and set its `value`, which is what will be rendered in the front end.

```php
$hardcover->setAttribute($attribute);

$hardcover->setValue('hardcover');
```

Finally, let’s find a product that will have your newly created attribute.

```php
/** @var ProductInterface $product */
$product = $this->container->get('sylius.repository.product')->findOneBy(['code' => 'code']);

$product->addAttribute($hardcover);
```

Now let’s see what has to be done if you would like to add an attribute of `integer` type. Let’s find such a one in the repository, it will be for example the `BOOK-PAGES` attribute.

```php
/** @var AttributeInterface $bookPagesAttribute */
$bookPagesAttribute = $this->container->get('sylius.repository.product_attribute')->findOneBy(['code' => 'BOOK-PAGES']);

/** @var AttributeValueInterface $pages */
$pages = $attributeValueFactory->createNew();

$pages->setAttribute($bookPagesAttribute);

$pages->setValue(500);

$product->addAttribute($pages);
```

After adding attributes remember to **flush the product manager**.

```php
$this->container->get('sylius.manager.product')->flush();
```

Your Product will now have two Attributes.
