Getting a Single Resource
=========================

Your newly created controller service supports basic CRUD operations and is configurable via routing.

The simplest action is **showAction**. It is used to display a single resource. To use it, the only thing you need to do is register a proper route.

Let's assume that you have a ``app.book`` resource registered. To display a single Book, define the following routing:

.. code-block:: yaml

    # app/config/routing.yml

    app_book_show:
        path: /books/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.book:showAction

Done! Now when you go to ``/books/3``, ResourceController will use the repository (``app.repository.book``) to find a Book with the given id (``3``).
If the requested book resource does not exist, it will throw a ``404 Not Found`` exception.

When a Book is found, the default template will be rendered - ``App:Book:show.html.twig`` (like you configured it in the ``config.yml``)
with the Book result as the ``book`` variable. That's the most basic usage of the simple ``showAction``.

Using a Custom Template
-----------------------

Okay, but what if you want to display the same Book resource, but with a different representation in a view?

.. code-block:: yaml

    # routing.yml

    app_admin_book_show:
        path: /admin/books/{id}
        methods: [GET]
        defaults:
            _controller: app.controller.book:showAction
            _sylius:
                template: Admin/Book/show.html.twig

Nothing more to do here, when you go to ``/admin/books/3``, the controller will try to find the Book and render
it using the custom template you specified under the route configuration. Simple, isn't it?

Overriding Default Criteria
---------------------------

Displaying books by id can be boring... and let's say we do not want to allow viewing disabled books. There is a solution for that!

.. code-block:: yaml

    # routing.yml

    app_book_show:
        path: /books/{title}
        methods: [GET]
        defaults:
            _controller: app.controller.book:showAction
            _sylius:
                criteria:
                    title: $title
                    enabled: true

With this configuration, the controller will look for a book with the given title and exclude disabled books.
Internally, it simply uses the ``$repository->findOneBy(array $criteria)`` method to look for the resource.

Using Custom Repository Methods
-------------------------------

By default, resource repository uses **findOneBy(array $criteria)**, but in some cases it's not enough - for example - you want to do proper joins or use a custom query.
Creating yet another action to change the method called could be a solution but there is a better way. The configuration below will use a custom repository method to get the resource.

.. code-block:: yaml

    # routing.yml

    app_book_show:
        path: /books/{author}
        methods: [GET]
        defaults:
            _controller: app.controller.book:showAction
            _sylius:
                repository:
                    method: findOneNewestByAuthor
                    arguments: [$author]

Internally, it simply uses the ``$repository->findOneNewestByAuthor($author)`` method, where ``author`` is taken from the current request.

Configuration Reference
-----------------------

.. code-block:: yaml

    # routing.yml

    app_book_show:
        path: /books/{author}
        methods: [GET]
        defaults:
            _controller: app.controller.book:showAction
            _sylius:
                template: Book/show.html.twig
                repository:
                    method: findOneNewestByAuthor
                    arguments: [$author]
                criteria:
                    enabled: true
                serialization_groups: [Custom, Details]
                serialization_version: 1.0.2
