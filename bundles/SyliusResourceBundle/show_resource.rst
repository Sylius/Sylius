Getting a single resource
=========================

.. note::

    ResourceController class is built using FOSRestBundle, thus it's format agnostic, and can serve resources in many formats, like html, json or xml.

Your newly created controller service has a few basic crud actions and is configurable via routing, which allows you to do some really tedious tasks - easily.

The most basic action is **showAction**. It is used to display a single resource. To use it, the only thing you need to do is register a proper route.

.. code-block:: yaml

    # routing.yml

    app_user_show:
        pattern: /users/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction

Done! Now when you go to ``/users/3``, ResourceController will use the repository (``app.repository.user``) to find user with given id.
If the requested user resource does not exist, it will throw a 404 exception.

When a user is found, the default template will be rendered - ``App:User:show.html.twig`` (like you configured it in `config.yml`) with the User result as the ``user`` variable.
That's the most basic usage of the simple ``showAction`` action.

Using a custom template
-----------------------

Okay, but what if you want now to display same User resource, but with a different representation?

.. code-block:: yaml

    # routing.yml

    app_backend_user_show:
        pattern: /backend/users/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.user:showAction
            _sylius:
                template: App:Backend/User:show.html.twig

Nothing more to do here, when you go to ``/backend/users/3``, the controller will try to find the user and render it using the custom template you specified under the route configuration.
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

With this configuration, the controller will look for a user with the given username and exlude disabled users.
Internally, it simply uses the ``$repository->findOneBy(array $criteria)`` method to look for the resource.

Using custom repository methods
-------------------------------

By default, resource repository uses **findOneBy(array $criteria)**, but in some cases it's not enough - for example - you want to do proper joins or use a custom query.
Creating yet another action to change the method called could be a solution but there is a better way. The configuration below will use a custom repository method to get the resource.

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

Internally, it simply uses the ``$repository->findOneWithFriends($username)`` method, where ``username`` is taken from the current request.
