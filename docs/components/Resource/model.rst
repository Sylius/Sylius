Model interfaces
================

.. _component_resource_model_resource-interface:

ResourceInterface
-----------------

This primary interface marks the model as a resource and will ask you to implement the following methods to your model:

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| getId()                            | Get identifier                           | mixed             |
+------------------------------------+------------------------------------------+-------------------+

.. _component_resource_model_timestampable-interface:

TimestampableInterface
----------------------

This interface will ask you to implement the following methods to your model, they will use by the
`timestampable Doctrine2 extension <https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/timestampable.md>`_.

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| getCreatedAt()                     | Get creation time                        | \DateTime         |
+------------------------------------+------------------------------------------+-------------------+
| setCreatedAt(\DateTime $createdAt) | Set creation time                        | void              |
+------------------------------------+------------------------------------------+-------------------+
| getUpdatedAt()                     | Get the time of last update              | \DateTime         |
+------------------------------------+------------------------------------------+-------------------+
| setUpdatedAt(\DateTime $updatedAt) | Set the time of last update              | void              |
+------------------------------------+------------------------------------------+-------------------+

.. _component_resource_model_toggleable-interface:

ToggleableInterface
-------------------

This interface can be applied to every toggleable model and will ask you to implement the following methods to your model:

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| isEnabled()                        | Return current status                    | bool              |
+------------------------------------+------------------------------------------+-------------------+
| enable()                           | Set as enabled                           | void              |
+------------------------------------+------------------------------------------+-------------------+
| disable()                          | Set as disabled                          | void              |
+------------------------------------+------------------------------------------+-------------------+
| setEnabled(bool $enabled)          | Set current status                       | void              |
+------------------------------------+------------------------------------------+-------------------+

.. _component_resource_model_code-aware-interface:

CodeAwareInterface
------------------

This interface can be applied to every code aware model and will ask you to implement the following methods to your model:

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| getCode()                          | Get code                                 | string            |
+------------------------------------+------------------------------------------+-------------------+
| setCode(string $code)              | Set code                                 | void              |
+------------------------------------+------------------------------------------+-------------------+

.. _component_resource_model_slug-aware-interface:

SlugAwareInterface
------------------

This interface is used by `sluggable Doctrine2 extension <https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/sluggable.md>`
and will ask you to implement the following methods to your model:

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| getSlug()                          | Get slug                                 | string            |
+------------------------------------+------------------------------------------+-------------------+
| setSlug(string $slug = null)       | Set slug                                 | void              |
+------------------------------------+------------------------------------------+-------------------+

.. _component_resource_model_translatable-interface:

TranslatableInterface
---------------------

This interface should be implemented by a model used in more than one language.

.. hint::
   Although you can implement this interface in your class, it's easier to just
   use the :ref:`component_resource_translations_translatable-trait` class.

.. note::
   For more detailed information go to `Sylius API TranslatableInterface`_.

.. _Sylius API TranslatableInterface: http://api.sylius.org/Sylius/Component/Resource/Model/TranslatableInterface.html

.. _component_resource_model_translation-interface:

TranslationInterface
--------------------

This interface should be implemented by a model responsible for keeping a single translation.

.. hint::
   And as above, although you are completely free to create your own class implementing this interface,
   it's already implemented in the :ref:`component_resource_translations_abstract-translation` class.

.. note::
   For more detailed information go to `Sylius API TranslationInterface`_.

.. _Sylius API TranslationInterface: http://api.sylius.org/Sylius/Component/Resource/Model/TranslationInterface.html
