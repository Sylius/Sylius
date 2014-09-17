Extra tools
===========

Parameters parser
-----------------

For each route defined the parameter parser allow you to find extra data in the current request, in the resource previously
created by using the `PropertyAccess component <http://symfony.com/doc/current/components/property_access/index.html>`_
or by using the `Expression Language component <http://symfony.com/doc/current/components/expression_language/index.html>`_

Request
+++++++

You need use the following syntax *$var_name*. It will try to find in the request the key named var_name *$request->get('var_name')*.

.. code-block:: yaml

    # routing.yml

    app_user_index:
        path: /products
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                criteria: {name: $name}


Resource previously created
+++++++++++++++++++++++++++

You need use the following syntax *resource.attribute_name*. It will try to find the value of the attribute name named
attribute_name *$accessor->getValue($resource, 'attribute_name'))*.

.. code-block:: yaml

    # routing.yml

    app_user_index:
        path: /products
        methods: [POST]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                redirect:
                    route: my_route
                    parameters: {name: resource.name}


Expression Language component
+++++++++++++++++++++++++++++

You need use the following syntax *expr:resource.my_expression*.

.. code-block:: yaml

    # routing.yml

    app_user_index:
        path: /customer/orders
        methods: [POST]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                method: findOrderByCustomer
                arguments: ['expr:service("security.context").getToken().getUser()']

.. note::

    * ``service``: returns a given service (see the example above);
    * ``parameter``: returns a specific parameter value, aka: ``expr:parameter("sylius.locale")``

Event Dispatcher
----------------

The methods ``ResourceController:createAction``, ``ResourceController:updateAction`` and ``ResourceController:deleteAction``
throw events before and after executing any actions on the current resource. The name of the events used the following pattern
``app_name.resource.(pre|post)_(create|update|delete)``.

First, you need to register event listeners, the following example show you how you can do that.

.. code-block:: xml

    # services.xml

    <service id="my_listener" class="MyBundle\MyEventListener%">
        <tag name="kernel.event_listener" event="app.user.pre_update" method="onOrderPreUpdate"/>
    </service>


.. code-block:: yaml

    # services.yml

    services:
        my_listener:
            class: MyBundle\MyEventListener
            tags:
                - { name: kernel.event_listener, event: sylius.order.pre_create, method: onOrderPreCreate }

After that, you need to create your listener

.. code-block:: yaml

    class MyEventListener
    {
        public function onOrderPreCreate(ResourceEvent $event)
        {
            // You can get your resource like that
            $resource = $event->getSubject();

            // You can stop propagation too.
            $event->stop('my.message', array('%amount%' => $resource->getAmount()));
        }
    }

.. note::

    Caution: you can use subscribers too, you can get more informations `there <http://symfony.com/doc/current/cookbook/doctrine/event_listeners_subscribers.html>`_.
