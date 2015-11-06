Exceptions
==========

.. _component_registry_existing-service-exception:

ExistingServiceException
------------------------

This exception is thrown when you try to **register** a
service that is already in the registry.

.. note::
   This exception extends the `\\InvalidArgumentException`_.

.. _\\InvalidArgumentException: http://php.net/manual/en/class.invalidargumentexception.php

.. _component_registry_non-existing-service-exception:

NonExistingServiceException
---------------------------

This exception is thrown when you try to **unregister** a
service which is not in the registry.

.. note::
   This exception extends the `\\InvalidArgumentException`_.
