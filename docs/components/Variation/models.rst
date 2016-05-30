Models
======

.. _component_variation_model_variant:

Variant
-------

Every variant is represented by **Variant** instance and has the following properties:

+--------------+---------------------------------------------+
| Property     | Description                                 |
+==============+=============================================+
| id           | Unique id of the variant                    |
+--------------+---------------------------------------------+
| presentation | Name displayed to user                      |
+--------------+---------------------------------------------+
| object       | Related product                             |
+--------------+---------------------------------------------+
| options      | Option values                               |
+--------------+---------------------------------------------+
| createdAt    | Date of creation                            |
+--------------+---------------------------------------------+
| updatedAt    | Date of the last update                     |
+--------------+---------------------------------------------+

.. note::

    This model implements the :ref:`component_variation_model_variant-interface`.
    You will find more information about this interface in `Sylius API Variant`_.

.. _Sylius API Variant: http://api.sylius.org/Sylius/Component/Variation/Model/Variant.html

.. _component_variation_model_option:

Option
------

Every variant option is represented by **Option** instance and has the following properties:

+--------------+---------------------------------------------+
| Property     | Description                                 |
+==============+=============================================+
| id           | Unique id of the Option                     |
+--------------+---------------------------------------------+
| code         | Unique code of the Option                   |
+--------------+---------------------------------------------+
| name         | Internal name                               |
+--------------+---------------------------------------------+
| presentation | Name displayed to user                      |
+--------------+---------------------------------------------+
| values       | Option values                               |
+--------------+---------------------------------------------+
| createdAt    | Date of creation                            |
+--------------+---------------------------------------------+
| updatedAt    | Date of the last update                     |
+--------------+---------------------------------------------+

.. note::

    This model implements the :ref:`component_variation_model_option-interface`.
    You will find more information about this interface in `Sylius API Option`_.

.. _Sylius API Option: http://api.sylius.org/Sylius/Component/Variation/Model/Option.html

.. _component_variation_model_option-translation:

OptionTranslation
-----------------

Every variant option has a corresponding translation stored as an **OptionTranslation** instance and has the following properties:

+--------------+---------------------------------------------+
| Property     | Description                                 |
+==============+=============================================+
| id           | Unique id of the translation                |
+--------------+---------------------------------------------+
| presentation | Translated option name                      |
+--------------+---------------------------------------------+

.. note::

    This model implements the :ref:`component_variation_model_option-translation-interface`.
    You will find more information about this interface in `Sylius API OptionTranslation`_.

.. _Sylius API OptionTranslation: http://api.sylius.org/Sylius/Component/Variation/Model/OptionTranslation.html

.. _component_variation_model_option_value:

OptionValue
-----------

Every variant option value is represented by **OptionValue** instance and has the following properties:

+--------------+---------------------------------------------+
| Property     | Description                                 |
+==============+=============================================+
| id           | Unique id of the OptionValue                |
+--------------+---------------------------------------------+
| code         | Unique code of the OptionValue              |
+--------------+---------------------------------------------+
| value        | Option internal value                       |
+--------------+---------------------------------------------+
| option       | An instance of Option                       |
+--------------+---------------------------------------------+

.. note::

    This model implements the :ref:`component_variation_model_option-value-interface`.
    You will find more information about this interface in `Sylius API OptionValue`_.

.. _Sylius API OptionValue: http://api.sylius.org/Sylius/Component/Variation/Model/OptionValue.html
