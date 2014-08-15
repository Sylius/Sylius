Getting a paginated (or flat) list of resources
===============================================

To get a paginated list of users, we will use **indexAction** of our controller!
In the default scenario, it will return an instance of paginator, with a list of Users.

.. code-block:: yaml

    # routing.yml

    app_user_index:
        path: /users
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction

When you go to ``/users``, ResourceController will use the repository (``app.repository.user``) to create a paginator.
The default template will be rendered - ``App:User:index.html.twig`` with the paginator as the ``users`` variable.

Overriding the template and criteria
------------------------------------

Just like for the **showAction**, you can override the default template and criteria.

.. code-block:: yaml

    # routing.yml

    app_user_index_inactive:
        path: /users/inactive
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                criteria:
                    enabled: false
                template: App:User:inactive.html.twig

This action will render a custom template with a paginator only for disabled users.

Sorting collection or paginator
-------------------------------

Except filtering, you can also sort users.

.. code-block:: yaml

    # routing.yml

    app_user_index_top:
        path: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                sorting:
                    score: desc
                template: App:User:top.html.twig

Under that route, you can paginate over the users by their score.

Changing the "max per page" option of paginator
-----------------------------------------------

You can also control the "max per page" for paginator, using ``paginate`` parameter.

.. code-block:: yaml

    # routing.yml

    app_user_index_top:
        path: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                paginate: 5
                sorting:
                    score: desc
                template: App:User:top.html.twig

This will paginate users by 5 per page, where 10 is the default.

Disabling pagination - getting flat list
----------------------------------------

Pagination is handy, but you do not always want to do it, you can disable pagination and simply request a collection of resources.

.. code-block:: yaml

    # routing.yml

    app_user_index_top3:
        path: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                paginate: false
                limit: 3
                sorting:
                    score: desc
                template: App:User:top3.html.twig

That action will return the top 3 users by score, as the ``users`` variable.

Twig Extensions
---------------

sylius_resource_sort
--------------------

**Parameters :**
    - **property (string) :** [Mandatory] Name of the property (defined in your resource)
    - **label (string) :** Label of the column on your grid (default : property name)
    - **order (string) :** Default order, it can be asc or desc (default : asc)
    - **options (array) :** Additional options, the extension can use a custom template or generate a custom route
        + **template (string) :** Path to the template
        + **route (string) :** Key of the new route
        + **route_params (array) :** Additional route parameters

This extension renders the following template : SyliusResourceBundle:Twig:paginate.html.twig

**Example :**

.. code-block:: html

    <div>
        {{ sylius_resource_sort('productId', 'product.id'|trans) }}
    </div>

sylius_resource_paginate
------------------------

**Parameters :**
    - **paginator (object) :** [Mandatory] An instance of PagerFanta
    - **limits (array) :** [Mandatory] An array of paginate value
    - **options (array) :** Additional options, the extension can use a custom template or generate a custom route
        + **template (string) :** Path to the template
        + **route (string) :** Key of the new route
        + **route_params (array) :** Additional route parameters

This extension renders the following template : SyliusResourceBundle:Twig:sorting.html.twig

**Example :**

.. code-block:: html

    <div>
        {{ sylius_resource_paginate(paginator, [10, 20, 30]) }}
    </div>
    