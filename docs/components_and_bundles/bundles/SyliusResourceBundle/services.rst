Services
========

When you register an entity as a resource, several services are registered for you.
For the ``app.book`` resource, the following services are available:

* ``app.controller.book`` - instance of ``ResourceController``;
* ``app.factory.book`` - instance of :ref:`component_resource_factory_factory-interface`;
* ``app.repository.book`` - instance of :ref:`component_resource_repository_repository-interface`;
* ``app.manager.book`` - alias to an appropriate Doctrine's ``ObjectManager``.
