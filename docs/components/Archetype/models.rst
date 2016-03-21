Models
======

.. _component_archetype_model_archetype:

Archetype
---------

Every archetype is represented by the **Archetype** instance and has the following properties:

+--------------+-------------------------------------------------------------------------------------------------------+
| Property     | Description                                                                                           |
+==============+=======================================================================================================+
| id           | Unique id of the archetype                                                                            |
+--------------+-------------------------------------------------------------------------------------------------------+
| code         | Machine-friendly name of the archetype ("car", "shoe", "bean_bag")                                    |
+--------------+-------------------------------------------------------------------------------------------------------+
| attributes   | Attributes to add when building new objects based on the archetype                                    |
+--------------+-------------------------------------------------------------------------------------------------------+
| options      | Options to add when building new objects based on the archetype                                       |
+--------------+-------------------------------------------------------------------------------------------------------+
| parent       | The parent archetype to inherit from (examples might be 'Vehicle' for 'Car' or 'Footwear' for 'Shoe') |
+--------------+-------------------------------------------------------------------------------------------------------+
| createdAt    | Date when attribute was created                                                                       |
+--------------+-------------------------------------------------------------------------------------------------------+
| updatedAt    | Date of last attribute update                                                                         |
+--------------+-------------------------------------------------------------------------------------------------------+


.. note::

    This model implements the :ref:`component_archetype_model_archetype-interface`.
    You can find more information about this class in `Sylius API Archetype`_.

.. _Sylius API Archetype: http://api.sylius.org/Sylius/Component/Archetype/Model/Archetype.html

.. _component_archetype_model_archetype-translation:

ArchetypeTranslation
--------------------

An **Archetype** instance needs also a corresponding **ArchetypeTranslation**.

+--------------+--------------------------------------------+
| Property     | Description                                |
+==============+============================================+
| id           | Unique id of the archetype's translation   |
+--------------+--------------------------------------------+
| name         | Name of the archetype 's translation       |
+--------------+--------------------------------------------+

.. note::

    This model implements the :ref:`component_archetype_model_archetype-translation-interface`.
    You can find more information about this class in `Sylius API ArchetypeTranslation`_.

.. _Sylius API ArchetypeTranslation: http://api.sylius.org/Sylius/Component/Archetype/Model/ArchetypeTranslation.html
