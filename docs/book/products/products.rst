.. index::
   single: Products

Products
========

**Product** model represents unique products in your Sylius store.
Every product can have different variations and attributes.

.. warning::

   Each product has to have at least one variant to be sold in the shop.

How to create a Product?
------------------------

Before we learn how to create products that can be sold, let's see how to create a product without its complex dependencies.

.. code-block:: php

     /** @var ProductFactoryInterface $productFactory **/
     $productFactory = $this->get('sylius.factory.product');

     /** @var ProductInterface $product */
     $product = $productFactory->createNew();

Creating an empty product is not enough to save it in the database. It needs to have a ``name`` and ``code``.

.. code-block:: php

     $product->setName('T-Shirt');
     $product->setCode('00001');

     /** @var RepositoryInterface $productRepository */
     $productRepository = $this->get('sylius.repository.product');

     $productRepository->add($product);

After being added via the repository, your product will be in the system. But the customers won't be able to buy it.

Variants
========

**ProductVariant** represents a unique kind of product and can have its own pricing configuration, inventory tracking etc.

Variants may be created out of Options of the product, but you are also able to use product variations system without the options at all.

Virtual Product Variants, that do not require shipping
------------------------------------------------------

.. tip::

     On the ProductVariant there is a possibility to make a product virtual - by setting its ``shippingRequired`` property to ``false``.
     In such a way you can have products that will be downloadable or installable for instance.

How to create a Product with a Variant?
---------------------------------------

You may need to sell product in different Variants - for instance you may need to have books both in hardcover and in paperback.
Just like before, use a factory, create the product, save it in the Repository.
And then using the ProductVariantFactory create a variant for your product.

.. code-block:: php

     /** @var ProductVariantFactoryInterface $productvariantFactory **/
     $productVariantFactory = $this->get('sylius.factory.product_variant');

     /** @var ProductVariantInterface $product */
     $variant = $productVariantFactory->createNew();

Having Variant created give it a desired name and attach it to your Product.

.. code-block:: php

     $variant->setName('Hardcover');
     $variant->setProduct($product);

Finally save your Variant in the database using a repository.

.. code-block:: php

     /** @var RepositoryInterface $productVariantRepository */
     $productVariantRepository = $this->get('sylius.repository.product_variant');

     $productVariantRepository->add($variant);

Options
=======

In many cases, you will want to have product with different variations.
The simplest example would be a piece of clothing, like a T-Shirt available in different sizes and colors
or a glass available in different shapes or colors.
In order to automatically generate appropriate variants, you need to define options.

Every option type is represented by **ProductOption** and references multiple **ProductOptionValue** entities.

For example you can have two options - Size and Color. Each of them will have their own values.

* Size
    * S
    * M
    * L
    * XL
    * XXL

* Color
    * Red
    * Green
    * Blue

After defining possible options for a product let's move on to **Variants** which are in fact combinations of options.

How to create a Product with Options and Variants?
--------------------------------------------------

Firstly let's learn how to prepare an exemplary Option and its values.

.. code-block:: php

     /* @var $option OptionInterface */
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
         /* @var $values OptionValueInterface */
         $optionValue = $this->get('sylius.factory.product_option_value')->createNew();

         $optionValue->setCode($code);
         $optionValue->setFallbackLocale($values['locale']);
         $optionValue->setCurrentLocale($values['locale']);
         $optionValue->setValue($values['value']);

         $option->addValue($optionValue);
     }

After you have an Option created and you keep it as ``$option`` variable let's add it to the Product and generate **Variants**.

.. code-block:: php

     // Assuming that you have a basic product let's add the previously created option to it.
     $product->addOption($option);

     // Having option of a product you can generate variants. Sylius has a service for that operation.
     /** @var VariantGeneratorInterface $variantGenerator */
     $variantGenerator = $this->get('sylius.generator.product_variant');

     $variantGenerator->generate($product);

     // And finally add the product, with its newly generated variants to the repository.
     /** @var RepositoryInterface $productRepository */
     $productRepository = $this->get('sylius.repository.product');

     $productRepository->add($product);

Learn more:
-----------

* :doc:`Product - Bundle Documentation </bundles/SyliusProductBundle/index>`
* :doc:`Product - Component Documentation </components/Product/index>`
