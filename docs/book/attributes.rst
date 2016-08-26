.. index::
   single: Attributes

Attributes
==========

Attributes in Sylius are used to describe traits shared among entities. The best example are products, that may be of
the same category and therefore they will have many similar attributes such as **number of pages for a book**,
**brand of a T-shirt** or simply **details of any product**.

Attribute
---------

The **Attribute** model has a translatable name (like for instance ``Book pages``), code (``book_pages``) and type (``integer``).
There are a few available types of an Attribute:

* text (*default*)
* checkbox
* integer
* percent
* textarea

What these types may be useful for?

- text - brand of a T-Shirt
- checkbox - show whether a T-Shirt is made of cotton or not
- integer - number of elements when a product is a set of items.
- percent - show how much cotton is there in a piece of clothing
- textarea - display more detailed information about a product

How to create an Attribute?
---------------------------

To give you a better insight into Attributes, let's have a look how to prepare and add an Attribute with a Product to the system programatically.

To assign Attributes to Products firstly you will need a factory for ProductAttributes.
The AttributeFactory has a special method createTyped($type), where $type is a string.

.. code-block:: php

   /** @var AttributeFactoryInterface $attributeFactory */
   $attributeFactory = $this->get('sylius.factory.product_attribute');

   /** @var AttributeInterface $attribute */
   $attribute = $attributeFactory->createTyped('text');
   $attribute->setName('Book cover');

In order to assign value to your Attribute you will need a factory of ProductAttributeValues,
use it to create a new value object.

.. code-block:: php

   /** @var FactoryInterface $attributeValueFactory */
   $attributeValueFactory = $this->get('sylius.factory.product_attribute_value');

   /** @var AttributeValueInterface $hardcover */
   $hardcover = $attributeValueFactory->createNew();

Attach the new AttributeValue to your Attribute and set its ``value``, which is what will be rendered in frontend.

.. code-block:: php

   $hardcover->setAttribute($attribute);

   $hardcover->setValue('hardcover');

Finally let's prepare a product that will have your newly created attribute and then add it to the system using a repository.

.. code-block:: php

   /** @var ProductFactoryInterface */
   $productFactory = $this->get('sylius.factory.product');
   /** @var ProductInterface $product */
   $product = $productFactory->createNew();

   $product->addAttribute($hardcover);

   /** @var productRepositoryInterface $productRepository */
   $productRepository = $this->get('sylius.repository.product');

   $productRepository->add($product);

Your Product will now have an Attribute with two possible values.

Learn more
----------

* :doc:`Attribute - Component Documentation </components/Attribute/index>`
