Interfaces
==========

Model Interfaces
----------------

.. _component_variation_model_variable-interface:

VariableInterface
~~~~~~~~~~~~~~~~~

In order for the object class to manage variants and options it has to implement the ``VariableInterface``.

.. note::

    You will find more information about this interface in `Sylius API VariableInterface`_.

.. _Sylius API VariableInterface: http://api.sylius.org/Sylius/Component/Variation/Model/VariableInterface.html

.. _component_variation_model_variant-interface:

VariantInterface
~~~~~~~~~~~~~~~~

When an object class implements the ``VariantInterface`` it has a possibility to manage options.

.. note::

    This interface extends the :ref:`component_resource_model_timestampable-interface`.

    You will find more information about this interface in `Sylius API VariantInterface`_.

.. _Sylius API VariantInterface: http://api.sylius.org/Sylius/Component/Variation/Model/VariantInterface.htm

.. _component_variation_model_option-interface:

OptionInterface
~~~~~~~~~~~~~~~

In order for an object class to represent the option type it has to implement the ``OptionInterface``.

.. note::

    This interface extends :ref:`component_resource_model_code-aware-interface`, :ref:`component_resource_model_timestampable-interface`
    and the :ref:`component_variation_model_option-translation-interface`.

    You will find more information about this interface in `Sylius API OptionInterface`_.

.. _Sylius API OptionInterface: http://api.sylius.org/Sylius/Component/Variation/Model/OptionInterface.html

.. _component_variation_model_option-translation-interface:

OptionTranslationInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to store the translation of an **Option** an object class needs to imlement this interface.

.. note::

    You will find more information about this interface in `Sylius API OptionTranslationInterface`_.

.. _Sylius API OptionTranslationInterface: http://api.sylius.org/Sylius/Component/Variation/Model/OptionTranslationInterface.html

.. _component_variation_model_option-value-interface:

OptionValueInterface
~~~~~~~~~~~~~~~~~~~~

If you need to store a value of an **Option** you will have to create an object class that implements this interface.

.. note::

    This interface extends :ref:`component_resource_model_code-aware-interface`.

    You will find more information about that interface in `Sylius API OptionValueInterface`_.

.. _Sylius API OptionValueInterface: http://api.sylius.org/Sylius/Component/Variation/Model/OptionValueInterface.html

Services Interfaces
-------------------

.. _component_variation_set-builder_set-builder-interface:

SetBuilderInterface
~~~~~~~~~~~~~~~~~~~

When you want a service to be able to Build a product set from one or more given sets it should implement the ``SetBuilderInterface``.

.. note::

    You will find more information about that interface in `Sylius API SetBuilderInterface`_.

.. _Sylius API SetBuilderInterface: http://api.sylius.org/Sylius/Component/Variation/SetBuilder/SetBuilderInterface.html

.. _component_variation_generator_variant-generator-interface:

VariantGeneratorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

This interface is used to create all possible (non-existing) variations of a given object based on its options.

.. note::

    You will find more information about that interface in `Sylius API VariantGeneratorInterface`_.

.. _Sylius API VariantGeneratorInterface: http://api.sylius.org/Sylius/Component/Variation/SetBuilder/VariantGeneratorInterface.html
