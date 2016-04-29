Services
========

When you register an entity as a resource, there are several services registered for you. For ``app.book`` resource, following services are available:

* ``app.controller.book`` instanceof ``ResourceController``;
* ``app.factory.book`` instance of :ref:`component_resource_factory_factory-interface`;
* ``app.repository.book`` instance of :ref:`component_resource_repository_repository-interface`;
* ``app.manager.book`` alias to appropriate Doctrine's ``ObjectManager``.
