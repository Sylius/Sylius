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
