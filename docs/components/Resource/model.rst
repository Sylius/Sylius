Model interfaces
================

.. _component_resource_model_timestampable-interface:

TimestampableInterface
----------------------

This interface will ask you to implement the following methods to your model, they will use by the
`timestampable Doctrine2 extension <https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/timestampable.md/>`_. :

+------------------------------------+------------------------------------------+-------------------+
| Method                             | Description                              | Returned value    |
+====================================+==========================================+===================+
| getCreatedAt()                     | Get creation time                        | \DateTime         |
+------------------------------------+------------------------------------------+-------------------+
| getUpdatedAt()                     | Get the time of last update              | \DateTime         |
+------------------------------------+------------------------------------------+-------------------+
| setCreatedAt(\DateTime $createdAt) | Set creation time                        | Void              |
+------------------------------------+------------------------------------------+-------------------+
| setUpdatedAt(\DateTime $updatedAt) | Set the time of last update              | Void              |
+------------------------------------+------------------------------------------+-------------------+
