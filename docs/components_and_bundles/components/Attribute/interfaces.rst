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
    This interface extends the `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_ and
    `ToggleableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/ToggleableInterface.php>`_
    and `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_.

    For more detailed information go to `Sylius Attribute Component AttributeInterface <https://github.com/Sylius/Attribute/blob/master/Model/AttributeInterface.php>`_.

.. _component_attribute_model_attribute-value-interface:

AttributeValueInterface
~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models used for
binding an :ref:`component_attribute_model_attribute`
with a model implementing the :ref:`component_attribute_model_attribute-subject-interface`
e.g. the :ref:`component_product_model_product`.

.. note::
    For more detailed information go to `Sylius Attribute Component AttributeValueInterface <https://github.com/Sylius/Attribute/blob/master/Model/AttributeValueInterface.php>`_.

.. _component_attribute_model_attribute-translation-interface:

AttributeTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models maintaining a translation
of an :ref:`component_attribute_model_attribute` for specified locale.

.. note::
    For more detailed information go to `Sylius Attribute Component AttributeTranslationInterface <https://github.com/Sylius/Attribute/blob/master/Model/AttributeTranslationInterface.php>`_.

.. _component_attribute_model_attribute-subject-interface:

AttributeSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models you want to characterize with
various :ref:`component_attribute_model_attribute-value` objects.

It will ask you to implement the management of :ref:`component_attribute_model_attribute-value` models.

.. note::
    For more detailed information go to `Sylius Attribute Component AttributeSubjectInterface <https://github.com/Sylius/Attribute/blob/master/Model/AttributeSubjectInterface.php>`_.
