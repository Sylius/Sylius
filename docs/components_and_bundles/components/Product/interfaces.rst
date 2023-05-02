.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_product_model_product-interface:

ProductInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by models characterizing a product.

.. note::
   This interface extends `SlugAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/SlugAwareInterface.php>`_,
   `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_
   and :ref:`component_product_model_product-translation-interface`.

.. _component_product_model_product-translation-interface:

ProductTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models used for storing a single translation of product fields.

.. note::
   This interface extends the `SlugAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/SlugAwareInterface.php>`_.

.. _component_product_model_attribute-value-interface:

AttributeValueInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interfaces should be implemented by models used
to bind an attribute and a value to a specific product.

.. note::
   This interface extends the :ref:`component_attribute_model_attribute-value-interface`.

.. _component_product_model_variant-interface:

ProductVariantInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models binding a product with a specific combination of attributes.
