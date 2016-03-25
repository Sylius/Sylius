Routing
=======

SyliusResourceBundle ships with a custom route loader that can save you some time.

Generating Generic CRUD Routing
-------------------------------

To generate a full CRUD routing, simply configure it in your ``app/config/routing.yml``:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
        type: sylius.resource

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method        Scheme Host Path
    app_book_show   GET           ANY    ANY  /books/{id}
    app_book_index  GET           ANY    ANY  /books/
    app_book_create GET|POST      ANY    ANY  /books/new
    app_book_update GET|PUT|PATCH ANY    ANY  /books/{id}/edit
    app_book_delete DELETE        ANY    ANY  /books/{id}

Using a Custom Path
-------------------

By default, Sylius will use a plural form of the resource name, but you can easily customize the path:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            path: library
        type: sylius.resource

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method        Scheme Host Path
    app_book_show   GET           ANY    ANY  /library/{id}
    app_book_index  GET           ANY    ANY  /library/
    app_book_create GET|POST      ANY    ANY  /library/new
    app_book_update GET|PUT|PATCH ANY    ANY  /library/{id}/edit
    app_book_delete DELETE        ANY    ANY  /library/{id}

Generating API CRUD Routing
---------------------------

To generate a full API-friendly CRUD routing, add these YAML lines to your ``app/config/routing.yml``:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
        type: sylius.resource_api

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method    Scheme Host Path
    app_book_show   GET       ANY    ANY  /books/{id}
    app_book_index  GET       ANY    ANY  /books/
    app_book_create POST      ANY    ANY  /books/
    app_book_update PUT|PATCH ANY    ANY  /books/{id}
    app_book_delete DELETE    ANY    ANY  /books/{id}

Excluding Routes
----------------

If you want to skip some routes, simply use ``except`` configuration:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            except: ['delete', 'update']
        type: sylius.resource

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method        Scheme Host Path
    app_book_show   GET           ANY    ANY  /books/{id}
    app_book_index  GET           ANY    ANY  /books/
    app_book_create GET|POST      ANY    ANY  /books/new

Generating Only Specific Routes
-------------------------------

If you want to generate some specific routes, simply use ``only`` configuration:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            only: ['show', 'index']
        type: sylius.resource

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method        Scheme Host Path
    app_book_show   GET           ANY    ANY  /books/{id}
    app_book_index  GET           ANY    ANY  /books/

Generating Routing for a Section
--------------------------------

Sometimes you want to generate routing for different "sections" of an application:

.. code-block:: yaml

    app_admin_book:
        resource: |
            alias: app.book
            section: admin
        type: sylius.resource
        prefix: /admin

    app_library_book:
        resource: |
            alias: app.book
            section: library
            only: ['show', 'index']
        type: sylius.resource
        prefix: /library

Results in the following routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name                   Method        Scheme Host Path
    app_admin_book_show    GET           ANY    ANY  /admin/books/{id}
    app_admin_book_index   GET           ANY    ANY  /admin/books/
    app_admin_book_create  GET|POST      ANY    ANY  /admin/books/new
    app_admin_book_update  GET|PUT|PATCH ANY    ANY  /admin/books/{id}/edit
    app_admin_book_delete  DELETE        ANY    ANY  /admin/books/{id}
    app_library_book_show  GET           ANY    ANY  /library/books/{id}
    app_library_book_index GET           ANY    ANY  /library/books/

Using Custom Templates
----------------------

By default, ``ResourceController`` will use the templates namespace you have configured for the resource.
You can easily change that per route, but it is also easy when you generate the routing:

.. code-block:: yaml

    app_admin_book:
        resource: |
            alias: app.book
            section: admin
            templates: :Admin/Book
        type: sylius.resource
        prefix: /admin

Following templates will be used for actions:

* ``:Admin/Book:show.html.twig``
* ``:Admin/Book:index.html.twig``
* ``:Admin/Book:create.html.twig``
* ``:Admin/Book:update.html.twig``

Using a Custom Form
-------------------

If you want to use a custom form:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            form: app_book_admin
        type: sylius.resource

``create`` and ``update`` actions will use ``app_book_admin`` form type.

Using a Custom Redirect
-----------------------

By default, after successful resource creation or update, Sylius will redirect to the ``show`` route and fallback to ``index`` if it does not exist.
If you want to change that behavior, use the following configuration:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
            redirect: update
        type: sylius.resource
