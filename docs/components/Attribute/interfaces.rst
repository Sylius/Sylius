Interfaces
==========

Model Interfaces
----------------

.. _component_attribute_model_attribute-interface:

AttributeInterface
~~~~~~~~~~~~~~~~~~

This interface should be implemented by models
used for describing a product's attribute.

.. note::
   This interface extends the :ref:`component_resource_model_timestampable-interface` and
   the :ref:`component_attribute_model_attribute-translation-interface`.

   For more detailed information go to `Sylius API AttributeInterface`_.

.. _Sylius API AttributeInterface: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeInterface.html

.. _component_attribute_model_attribute-value-interface:

AttributeValueInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models used for
binding an :ref:`component_attribute_model_attribute`
with a model implementing the :ref:`component_attribute_model_attribute-subject-interface`
e.g. the :ref:`component_product_model_product`.

.. note::
   For more detailed information go to `Sylius API AttributeValueInterface`_.

.. _Sylius API AttributeValueInterface: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeValueInterface.html

.. _component_attribute_model_attribute-translation-interface:

AttributeTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models maintaining a single translation
of an :ref:`component_attribute_model_attribute` for specified locale.

.. note::
   For more detailed information go to `Sylius API AttributeTranslationInterface`_.

.. _Sylius API AttributeTranslationInterface: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeTranslationInterface.html

.. _component_attribute_model_attribute-subject-interface:

AttributeSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models you want to characterize with
various :ref:`component_attribute_model_attribute-value` objects.

It will ask you to implement the management of :ref:`component_attribute_model_attribute-value` models.

.. note::
   For more detailed information go to `Sylius API AttributeSubjectInterface`_.

.. _Sylius API AttributeSubjectInterface: http://api.sylius.org/Sylius/Component/Attribute/Model/AttributeSubjectInterface.html

.. _component_attribute_model_attribute-type-interface:

AttributeTypeInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models
used for describing a product's attribute type.
