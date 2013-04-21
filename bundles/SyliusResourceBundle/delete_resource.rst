Usage
=====

To benefit from bundle services, you have to first register your class as *resource*.

Registering model as resource
-----------------------------

.. code-block:: yaml

    sylius_resources:
        resources:
            app.user:
                driver: doctrine/orm
                templates: App:User
                classes:
                    model: App\Entity\User

This configuration registers for you several services and service aliases.

First of all, it gives you **app.manager.user**, which is simple alias to proper **ObjectManager** service.
For *doctrine/orm* it will be your default entity manager, and unless you want to stay completely storage agnostic, you can use
the entity (or document) manager the "usual way".

Secondly, you get an **app.repository.user**. It represents repository. This service by default has custom class, which implements
``Sylius\\Bundle\\ResourceBundle\\Model\\RepositoryInterface`` (which extends the Doctrine **ObjectRepository**).

The last and most important service is **app.controller.user**, you'll learn about it in next part.

Getting single resource
-----------------------

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

Getting paginated (or flat) list of resources
---------------------------------------------

To get a paginated list of users, we will use **indexAction** of our controller!
In the default scenario, it will return instance of paginator, with a list of Users.

.. code-block:: yaml

    # routing.yml

    app_user_index:
        pattern: /users
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction

When you go to ``/users``, ResourceController will use the repository (``app.repository.user``) to create a paginator.
The default template will be rendered - ``App:User:index.html.twig`` with the paginator as ``users`` variable.

Just like for the **showAction**, you can override the default template and criteria.

.. code-block:: yaml

    # routing.yml

    app_user_index_inactive:
        pattern: /users/inactive
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                criteria:
                    enabled: false
                template: App:User:inactive.html.twig

This action will render custom template with a paginator only for disabled users.

Except filtering, you can also sort users.

.. code-block:: yaml

    # routing.yml

    app_user_index_top:
        pattern: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                sorting:
                    score: desc
                template: App:User:top.html.twig

Under that route, you can paginate over the users by their score.

You can also control the "max per page" for paginator, using ``paginate`` parameter.

.. code-block:: yaml

    # routing.yml

    app_user_index_top:
        pattern: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                paginate: 5
                sorting:
                    score: desc
                template: App:User:top.html.twig

This will paginate users by 5 per page, where 10 is the default.

Pagination is handy, but you do not always want to do it, you can disable pagination and simply request a collection of resources.

.. code-block:: yaml

    # routing.yml

    app_user_index_top3:
        pattern: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                paginate: false
                limit: 3
                sorting:
                    score: desc
                template: App:User:top3.html.twig

That action will return top 3 users by score, as ``users`` variable.
