Creating new resource
=====================

To display a form, handle submit or create a new resource via API, you should use **createAction** of your **app.controller.user** service.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction

Done! Now when you go to ``/users/new``, ResourceController will use the repository (``app.repository.user``) to create new user instance.
Then it will try to create an ``app_user`` form, and set the newly created user as data.

.. note::

    Currently, this bundle does not generate a form for you, the right form type has to be created and registered in the container manually.

As a response, it will render the ``App:User:create.html.twig`` template with form view as the ``form`` variable.

Submitting the form
-------------------

You can use exactly the same route to handle the submit of the form and create the user.

.. code-block:: html

    <form method="post" action="{{ path('app_user_create') }}">

On submit, the create action with method POST, will bind the request on the form, and if it is valid it will use the right manager to persist the resource.
Then, by default it redirects to ``app_user_show`` to display the created user, but you can easily change that behavior - you'll see this in later sections.

When validation fails, it will render the form just like previously with the errors to display.

Changing the template
---------------------

Just like for the **show** and **index** actions, you can customize the template per route.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                template: App:Backend/User:create.html.twig

Using different form
--------------------

You can also use custom form type on per route basis. By default it generates the form type name following the simple convention ``bundle prefix + _ + resource logical name``.
Below you can see the usage for specifying a custom form.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                template: App:Backend/User:create.html.twig
                form: app_user_custom

Using custom factory method
---------------------------

By default, ``ResourceController`` will use the ``createNew`` method with no arguments to create a new instance of your object. However, this behavior can be modified.
To use different method of your repository, you can simply configure the ``factory`` option.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                factory: createNewWithGroups

Additionally, if you want to provide your custom method with arguments from the request, you can do so by adding more parameters.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/{groupId}/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                factory: 
                    method: createNewWithGroups
                    arguments: [$groupId]

With this configuration, ``$repository->createNewWithGroups($request->get('groupId'))`` will be called to create new resource within ``createAction``.

Custom redirect after success
-----------------------------

By default the controller will try to get the id of the newly created resource and redirect to the "show" route. You can easily change that.
For example, to redirect user to list after successfully creating a new resource - you can use the following configuration.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                redirect: app_user_index

You can also perform more complex redirects, with parameters. For example...

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /competition/{competitionId}/users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                redirect:
                    route: app_competition_show
                    parameters: { id: $competitionId }

In addition to the request parameters, you can access some of the newly created objects properties, using the ``object.`` prefix.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction
            _sylius:
                redirect:
                    route: app_user_show
                    parameters: { email: object.email }

With this configuration, the ``email`` parameter for route ``app_user_show`` will be obtained from your newly created user.
