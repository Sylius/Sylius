Models
======

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
   This model uses the :ref:`component_resource_translations_translatable-trait`
   and implements the :ref:`component_product_model_product-interface`.

   For more detailed information go to `Sylius API Product`_.

.. _Sylius API Product: http://api.sylius.org/Sylius/Component/Product/Model/Product.html

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
   This model extends the :ref:`component_resource_translations_abstract-translation` class
   and implements the :ref:`component_product_model_product-translation-interface`.

   For more detailed information go to `Sylius API ProductTranslation`_.

.. _Sylius API ProductTranslation: http://api.sylius.org/Sylius/Component/Product/Model/ProductTranslation.html

.. _component_product_model_attribute-value:

AttributeValue
--------------

This **AttributeValue** extension ensures that it's **subject**
is an instance of the :ref:`component_product_model_product-interface`.

.. note::
   This model extends the :ref:`component_attribute_model_attribute-value`
   and implements the :ref:`component_product_model_attribute-value-interface`.

   For more detailed information go to `Sylius API AttributeValue`_.

.. _Sylius API AttributeValue: http://api.sylius.org/Sylius/Component/Product/Model/AttributeValue.html

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

   For more detailed information go to `Sylius API Variant`_.

.. _Sylius API Variant: http://api.sylius.org/Sylius/Component/Product/Model/Variant.html
