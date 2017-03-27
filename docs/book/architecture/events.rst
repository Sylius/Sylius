.. index::
   single: Events

Events
======

.. tip::

    You can learn more about events in general in `the Symfony documentation <http://symfony.com/doc/current/event_dispatcher.html>`_.

What is the naming convention of Sylius events?
-----------------------------------------------

The events that are designed for the entities have a general naming convention: ``sylius.entity_name.event_name``.

The examples of such events are: ``sylius.product.pre_update``, ``sylius.shop_user.post_create``, ``sylius.taxon.pre_create``.


Events reference
~~~~~~~~~~~~~~~~

All Sylius bundles are using :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>`, which has some built-in events.

+-------------------------------------+----------------------+
| Event                               | Description          |
+=====================================+======================+
| sylius.<resource>.pre_create        | Before persist       |
+-------------------------------------+----------------------+
| sylius.<resource>.post_create       | After flush          |
+-------------------------------------+----------------------+
| sylius.<resource>.pre_update        | Before flush         |
+-------------------------------------+----------------------+
| sylius.<resource>.post_update       | After flush          |
+-------------------------------------+----------------------+
| sylius.<resource>.pre_delete        | Before remove        |
+-------------------------------------+----------------------+
| sylius.<resource>.post_delete       | After flush          |
+-------------------------------------+----------------------+
| sylius.<resource>.initialize_create | Before creating view |
+-------------------------------------+----------------------+
| sylius.<resource>.initialize_update | Before creating view |
+-------------------------------------+----------------------+

CRUD events rules
~~~~~~~~~~~~~~~~~

As you should already know, every resource controller is represented by
the ``sylius.controller.<resource_name>`` service. Several useful events are dispatched during execution of every default action
of this controller. When creating a new resource via the ``createAction`` method, 2 events occur.

First, before the ``persist()`` is called on the resource, the ``sylius.<resource_name>.pre_create`` event is dispatched.

And after the data storage is updated, ``sylius.<resource_name>.post_create`` is triggered.

The same set of events is available for the ``update`` and ``delete`` operations.
All the dispatches are using the ``GenericEvent`` class and return the resource object by the ``getSubject`` method.

What events are already used in Sylius?
---------------------------------------

Even though Sylius has events as entry points to each resource only some of these points are already used in our usecases.

The events already used in Sylius are described in the Book alongside the concepts they concern.

.. tip::

    What is more you can easily check all the Sylius events in your application by using this command:

    .. code-block:: bash

        $ php bin/console debug:event-dispatcher | grep sylius

Customizations
--------------

.. note::

    **Customizing logic via Events vs. State Machines**

    The logic in which Sylius operates can be customized in two ways. First of them is using the state machines: what is
    really useful when you need to modify business logic for instance modify the flow of the checkout,
    and the second is listening on the kernel events related to the entities, which is helpful for modifying the HTTP responses
    visible directly to the user, like displaying notifications, sending emails.

Learn more
----------

* :doc:`Sylius Documentation: The Book </book/index>`
