Getting a Collection of Resources
=================================

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

A paginator can be a simple array if you disable the pagination otherwise it is a instance of ``Pagerfanta\Pagerfanta``
which is the `library <https://github.com/whiteoctober/Pagerfanta>`_ used to manage the pagination.

Overriding the Template and Criteria
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

Sorting
-------

Except filtering, you can also sort users.

.. code-block:: yaml

    # routing.yml

    app_user_index_top:
        path: /users/top
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                sortable: true
                sorting:
                    score: desc
                template: App:User:top.html.twig

Under that route, you can paginate over the users by their score.

Using a Custom Repository Method
--------------------------------

You can define your own repository method too, you can use the same way explained
in `show_resource  <http://docs.sylius.org/en/latest/bundles/SyliusResourceBundle/show_resource.html#using-custom-repository-methods>`_.

.. note::

    If you want to paginate your resources you need to use ``EntityRepository::getPaginator($queryBuilder)``.
    It will transform your doctrine query builder into ``Pagerfanta\Pagerfanta`` object.

Changing the "Max Per Page" Option of Paginator
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
                sortable: true
                sorting:
                    score: desc
                template: App:User:top.html.twig

This will paginate users by 5 per page, where 10 is the default.

Disabling Pagination - Getting a Simple Collection
--------------------------------------------------

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
                sortable: true
                sorting:
                    score: desc
                template: App:User:top3.html.twig

That action will return the top 3 users by score, as the ``users`` variable.

Configuration Reference
-----------------------

.. code-block:: yaml

    # routing.yml

    app_user_index:
        path: /{groupName}/users
        methods: [GET]
        defaults:
            _controller: app.controller.user:indexAction
            _sylius:
                template: :Group:users.html.twig
                repository:
                    method: createPaginatorByGroupName
                    arguments: [$groupName]
                criteria:
                    enabled: true
                    group.name: $groupName
                paginate: false # Or: 50
                limit: 100 # Or: false
                serialization_groups: [Custom, Details]
                serialization_version: 1.0.2
