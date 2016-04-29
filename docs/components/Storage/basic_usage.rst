Basic Usage
===========

Storages are services managing data in any type of container.

By default there are three storages available:

* :ref:`component_storage_cookie-storage`
* :ref:`component_storage_doctrine-cache-storage`
* :ref:`component_storage_session-storage`

.. tip::
   Feel free to implement your own storage, using any container you would like!

Using Storages
--------------

The methods of every **storage** work the same way. The only difference is the type of container we store in.

.. code-block:: php

   <?php

   use // Any service which implements the StorageInterface.

   $storage = // Your storage service.

   $key = 'aeiouy';
   $value = 'Hello!';

   $storage->setData($key, $value); // Insert your data to the storage.

   $storage->hasData($key); // Returns true as the key is in the storage.
   $storage->getData($key); // Returns 'Hello'.

   $storage->removeData($key); // Remove your data from the storage.

   $storage->hasData($key) // Now returns false as we removed the key
                           // from the storage along with it's value.

If the key which you try to get doesn't exist in the storage,
the ``getData`` method returns it's second parameter.
If it isn't specified, returns null.

.. code-block:: php

   $storage->getData('failure', 'Goodbye'); // Returns 'Goodbye'.
   $storage->getData('failure'); // Returns null.

.. _component_storage_cookie-storage:

Cookie Storage
--------------

**CookieStorage** is used to manage data in the cookies'
`ParameterBag`_ of Symfony's `Request`_ class, which needs
to be set in the storage before making any operations on it.

.. _ParameterBag: http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/ParameterBag.html
.. _Request: http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Request.html

.. code-block:: php

   <?php

   use Sylius\Component\Storage\CookieStorage;
   use Symfony\Component\HttpFoundation\Request;

   $request = // The request which cookies' data you would like to manage.

   $storage = new CookieStorage();

   $storage->setRequest($request);

.. note::
   This service implements :ref:`component_storage_storage-interface`.

.. _component_storage_doctrine-cache-storage:

Doctrine Cache Storage
----------------------

**DoctrineCacheStorage** is used to manage data in
objects implementing the Doctrine's `Cache`_ interface.

.. _Cache: http://www.doctrine-project.org/api/common/2.5/class-Doctrine.Common.Cache.Cache.html

.. code-block:: php

   <?php

   use Doctrine\Common\Cache\Cache;
   use Sylius\Component\Storage\DoctrineCacheStorage;

   $cache = // Your doctrine's cache.

   $storage = new DoctrineCacheStorage($cache);

.. note::
   This service implements :ref:`component_storage_storage-interface`.

.. _component_storage_session-storage:

Session Storage
---------------

**SessionStorage** is used to manage data in any class implementing the Symfony's `SessionInterface`_.

.. _SessionInterface: http://l3.shihan.me/api/class-Symfony.Component.HttpFoundation.Session.SessionInterface.html

.. code-block:: php

   <?php

   use Sylius\Component\Storage\SessionStorage;
   use Symfony\Component\HttpFoundation\Session\Session;

   $session = new Session();
   $session->start();

   $storage = new SessionStorage($session);

.. note::
   This service implements :ref:`component_storage_storage-interface`.
