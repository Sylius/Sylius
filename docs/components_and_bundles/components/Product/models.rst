.. rst-class:: outdated

Models
======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. _component_product_model_product:

Product
-------

The **Product** model represents every unique product in the catalog.
By default it contains the following properties:

+-----------------+-----------------------------------------------------------------------------+
| Property        | Description                                                                 |
+=================+=============================================================================+
| id              | Unique id of the product                                                    |
+-----------------+-----------------------------------------------------------------------------+
| name            | Product's name taken from the ``ProductTranslation``                        |
+-----------------+-----------------------------------------------------------------------------+
| slug            | Product's urlized name taken from the ``ProductTranslation``                |
+-----------------+-----------------------------------------------------------------------------+
| description     | Product's description taken from the ``ProductTranslation``                 |
+-----------------+-----------------------------------------------------------------------------+
| metaKeywords    | Product's meta keywords taken from the ``ProductTranslation``               |
+-----------------+-----------------------------------------------------------------------------+
| metaDescription | Product's meta description taken from the ``ProductTranslation``            |
+-----------------+-----------------------------------------------------------------------------+
| attributes      | Attributes assigned to this product                                         |
+-----------------+-----------------------------------------------------------------------------+
| variants        | Variants assigned to this product                                           |
+-----------------+-----------------------------------------------------------------------------+
| options         | Options assigned to this product                                            |
+-----------------+-----------------------------------------------------------------------------+
| createdAt       | Product's date of creation                                                  |
+-----------------+-----------------------------------------------------------------------------+
| updatedAt       | Product's date of update                                                    |
+-----------------+-----------------------------------------------------------------------------+

.. note::
   This model uses the `TranslatableTrait <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TranslatableTrait.php>`_
   and implements the :ref:`component_product_model_product-interface`.

.. _component_product_model_product-translation:

ProductTranslation
------------------

This model is responsible for keeping a translation
of product's simple properties according to given locale.
By default it has the following properties:

+-----------------+--------------------------------------+
| Property        | Description                          |
+=================+======================================+
| id              | Unique id of the product translation |
+-----------------+--------------------------------------+

.. note::
   This model extends the `AbstractTranslation <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/AbstractTranslation.php>`_ class
   and implements the :ref:`component_product_model_product-translation-interface`.

.. _component_product_model_attribute-value:

AttributeValue
--------------

This **AttributeValue** extension ensures that it's **subject**
is an instance of the :ref:`component_product_model_product-interface`.

.. note::
   This model extends the :ref:`component_attribute_model_attribute-value`
   and implements the :ref:`component_product_model_attribute-value-interface`.

.. _component_product_model_variant:

Variant
-------

This **Variant** extension ensures that it's **object**
is an instance of the :ref:`component_product_model_product-interface`
and provides an additional property:

+-------------+---------------------------------------------------------+
| Property    | Description                                             |
+=============+=========================================================+
| availableOn | The date indicating when a product variant is available |
+-------------+---------------------------------------------------------+

.. note::
   This model implements the :ref:`component_product_model_variant-interface`.
