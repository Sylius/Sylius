Getting single resource
=======================

.. note::

    ResourceController class is built using FOSRestBundle, thus it's format agnostic, and can serve resources in many formats, like html, json or xml.

Your newly created controller service has few basic crud actions and is configurable via routing, which allows you to do some really tedious tasks - easily.

The most basic action is **showAction**. It is used to display a single resource. To use it, only thing you need to do is register a proper route.

.. code-block:: yaml

    # routing.yml

    app_user_show:
        pattern: /users/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction

Done! Now when you go to ``/users/3``, ResourceController will use the repository (``app.repository.user``) to find user with given id.
If the requested user resource does not exist, it will throw 404 exception.

When user is found, the default template will be rendered - ``App:User:show.html.twig`` (like you configured in `config.yml`) with the User result as ``user`` variable.
That's the most basic usage of simple ``showAction``.

Using custom template
---------------------

Okay, but what if you want now to display same User resource, but with different representation?

.. code-block:: yaml

    # routing.yml

    app_backend_user_show:
        pattern: /backend/users/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction
            _sylius:
                template: App:Backend/User:show.html.twig

Nothing more to do here, when you go to ``/backend/users/3``, controller will try to find user and render the custom template you specified under the route configuration.
Simple, isn't it?

Overriding default criteria
---------------------------

Displaying the user by id can be boring... and let's say we do not want to allow viewing disabled users? There is a solution for that!

.. code-block:: yaml

    # routing.yml

    app_user_show:
        pattern: /users/{username}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction
            _sylius:
                criteria:
                    username: $username
                    enabled:  true

With this configuration, controller will look for user with given username and exlude disabled users.
Internally, it simply uses ``$repository->findOneBy(array $criteria)`` method to look for resource.

Using custom repository methods
-------------------------------

By default, resource repository uses **findOneBy(array $criteria)**, but in some cases it's not enough - for example - you want to do proper joins or use very custom query.
Creating yet another action to change the called method - you can avoid it. Configuration below will use custom repository method to get the resource.

.. code-block:: yaml

    # routing.yml

    app_user_show:
        pattern: /users/{username}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction
            _sylius:
                method: findOneWithFriends
                arguments: [$username]

Internally, it simply uses ``$repository->findOneWithFriends($username)`` method, where ``username`` is taken from current request.
