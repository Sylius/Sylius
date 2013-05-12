Creating new resource
=====================

To display a form, handle submit or create new resource via API, you should use **createAction** of your **app.controller.user** service.

.. code-block:: yaml

    # routing.yml

    app_user_create:
        pattern: /users/new
        methods: [GET, POST]
        defaults:
            _controller: app.controller.user:createAction

Done! Now when you go to ``/users/new``, ResourceController will use the repository (``app.repository.user``) to create new user instance.
Then it will try to create ``app_user`` form, set the newly created user as data.

.. note::

    Currently, bundle does not generate a form for you, the right form type has to be created and registered in container manually.

As a response, it will render ``App:User:create.html.twig`` template with form view as ``form`` variable.

Submitting the form
-------------------

You can use exactly the same route to handle the submit of the form and create the user.

.. code-block:: html

    <form method="post" action="{{ path('app_user_create') }}">

On submit, the create action with method POST, will bind the request on the form, and if it's valid - use the right manager to persist the resource.
Then, by default it redirects to ``app_user_show`` to display the created user, but you can easily change that behavior - you'll see in later parts.

When validation fails, it will render the form just like previously with the errors to display.

Changing the template
---------------------

Just like for **show** and **index** actions, you can customize the template per route.

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

You can also use custom form type on per route basis. By default it generates form type name by following simple convention ``bundle prefix + _ + resource logical name``.
Below you can see usage of custom form.

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

Custom redirect after success
-----------------------------

By default the controller will try to get the id of newly created resource and redirect to "show" route. You can easily change that.
For example, to redirect user to list after successfuly creating new resource - you can use following configuration.

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
