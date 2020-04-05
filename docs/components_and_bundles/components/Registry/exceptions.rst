.. rst-class:: outdated

Exceptions
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

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
