Interfaces
==========

Model Interfaces
----------------

.. _component_product_model_product-interface:

ProductInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by models characterizing a product.

.. note::
   This interface extends :ref:`component_resource_model_slug-aware-interface`,
   :ref:`component_resource_model_timestampable-interface`
   and :ref:`component_product_model_product-translation-interface`.

   For more information go to `Sylius API ProductInterface`_.

.. _Sylius API ProductInterface: http://api.sylius.org/Sylius/Component/Product/Model/ProductInterface.html

.. _component_product_model_product-translation-interface:

ProductTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models used for storing a single translation of product fields.

.. note::
   This interface extends the :ref:`component_resource_model_slug-aware-interface`.

   For more information go to `Sylius API ProductTranslationInterface`_.

.. _Sylius API ProductTranslationInterface: http://api.sylius.org/Sylius/Component/Product/Model/ProductTranslationInterface.html

.. _component_product_model_attribute-value-interface:

AttributeValueInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interfaces should be implemented by models used
to bind an attribute and a value to a specific product.

.. note::
   This interface extends the :ref:`component_attribute_model_attribute-value-interface`.

   For more information go to `Sylius API AttributeValueInterface`_.

.. _Sylius API AttributeValueInterface: http://api.sylius.org/Sylius/Component/Product/Model/AttributeValueInterface.html

.. _component_product_model_variant-interface:

ProductVariantInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models binding a product with a specific combination of attributes.
