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
* date
* datetime

What these types may be useful for?

- text - brand of a T-Shirt
- checkbox - show whether a T-Shirt is made of cotton or not
- integer - number of elements when a product is a set of items.
- percent - show how much cotton is there in a piece of clothing
- textarea - display more detailed information about a product
- date - release date of a movie
- datetime - accurate date and time of an event

How to create an Attribute?
---------------------------

To give you a better insight into Attributes, let's have a look how to prepare and add an Attribute with a Product to the system programatically.

To assign Attributes to Products firstly you will need a factory for ProductAttributes.
The AttributeFactory has a special method createWithType($type), where $type is a string.

The Attribute needs a ``code`` and a ``name`` before it can be saved in the repository.

.. code-block:: php

   /** @var AttributeFactoryInterface $attributeFactory */
   $attributeFactory = $this->container->get('sylius.factory.product_attribute');

   /** @var AttributeInterface $attribute */
   $attribute = $attributeFactory->createWithType('text');

   $attribute->setName('Book cover');
   $attribute->setCode('book_cover');

   $this->container->get('sylius.repository.product_attribute')->add($attribute);

In order to assign value to your Attribute you will need a factory of ProductAttributeValues,
use it to create a new value object.

.. code-block:: php

   /** @var FactoryInterface $attributeValueFactory */
   $attributeValueFactory = $this->container->get('sylius.factory.product_attribute_value');

   /** @var AttributeValueInterface $hardcover */
   $hardcover = $attributeValueFactory->createNew();

Attach the new AttributeValue to your Attribute and set its ``value``, which is what will be rendered in frontend.

.. code-block:: php

   $hardcover->setAttribute($attribute);

   $hardcover->setValue('hardcover');

Finally let's find a product that will have your newly created attribute.

.. code-block:: php

   /** @var ProductInterface $product */
   $product = $this->container->get('sylius.repository.product')->findOneBy(['code' => 'code']);

   $product->addAttribute($hardcover);

Now let's see what has to be done if you would like to add an attribute of ``integer`` type. Let's find such a one in the repository,
it will be for example the ``BOOK-PAGES`` attribute.

.. code-block:: php

   /** @var AttributeInterface $bookPagesAttribute */
   $bookPagesAttribute = $this->container->get('sylius.repository.product_attribute')->findOneBy(['code' => 'BOOK-PAGES']);

   /** @var AttributeValueInterface $pages */
   $pages = $attributeValueFactory->createNew();

   $pages->setAttribute($bookPagesAttribute);

   $pages->setValue(500);

   $product->addAttribute($pages);

After adding attributes remember to **flush the product manager**.

.. code-block:: php

   $this->container->get('sylius.manager.product')->flush();

Your Product will now have two Attributes.

Learn more
----------

* :doc:`Attribute - Component Documentation </components/Attribute/index>`
