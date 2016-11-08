Introduction to containers
==========================

In order to provide support for defining contexts and pages in Behat container with dependencies from Symfony application
container, our service definitions may contain some extra features.

There are 3 available containers:

  - ``behat`` (the default one) - the container which holds all Behat services, its extensions services, our contexts,
    pages and helper services used by them

  - ``symfony`` - the container which holds all the services defined in the application, the services retrieved from this
    container are isolated between scenarios

  - ``symfony_shared`` - the container which holds all the services defined in the application, created only once,
    the services retrieved from this container are not isolated between scenarios

Right now, you can only inject services from foreign containers into the default containers. To do so, prepend **service id**
with ``__CONTAINERNAME__.``:

.. code-block:: xml

    <service id="service.id" class="Class">
        <argument type="service" id="behat.service.id" />
        <argument type="service" id="__behat__.another.behat.service.id" />
        <argument type="service" id="__symfony__.symfony.service.id" />
        <argument type="service" id="__symfony_shared__.shared.symfony.service.id" />
    </service>
