Configuring Your Resources
==========================

Now you need to configure your first resource. Let's assume you have a *Book* entity in your application and it has simple fields:

* id
* title
* author
* description

In your class, you need to implement a simple interface:

.. code-block:: php

    <?php

    namespace AppBundle\Entity;

    use Sylius\Component\Resource\Model\ResourceInterface;

    class Book implements ResourceInterface
    {
        // Most of the time you have the code below already in your class.
        protected $id;

        public function getId()
        {
            return $this->id;
        }
    }

In your ``app/config/config.yml`` add:

.. code-block:: yaml

    sylius_resource:
        resources:
            app.book:
                classes:
                    model: AppBundle\Entity\Book

That's it! Your "Book" entity is now registered as Sylius Resource.

Do you want to try it out? Add following lines to ``app/config/routing.yml``:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
        type: sylius.resource_api

Full JSON/XML CRUD API is ready to use. Sounds crazy? Spin up the built-in server and give it a try:

.. code-block:: bash

    php app/console server:run

You should see something like:

.. code-block:: bash

    Server running on http://127.0.0.1:8000

    Quit the server with CONTROL-C.

Now, in a separate Terminal window, call these commands:

.. code-block:: bash

   curl -i -X POST -H "Content-Type: application/json" -d '{"title": "Lord of The Rings", "author": "J. R. R. Tolkien", "description": "Amazing!"}' http://localhost:8000/books/
   curl -i -X GET -H "Accept: application/json" http://localhost:8000/books/

As you can guess, other CRUD actions are available through this API. But, what if you want to render HTML pages? That's easy! Update the routing configuration:

.. code-block:: yaml

    app_book:
        resource: |
            alias: app.book
        type: sylius.resource

This will generate routing for HTML views.

Run the ``debug:router`` command to see available routes:

.. code-block:: bash

    php app/console debug:router

    [router] Current routes
    Name            Method        Scheme Host Path
    app_book_show   GET           ANY    ANY  /books/{id}
    app_book_index  GET           ANY    ANY  /books/
    app_book_create GET|POST      ANY    ANY  /books/new
    app_book_update GET|PUT|PATCH ANY    ANY  /books/{id}/edit
    app_book_delete DELETE        ANY    ANY  /books/{id}

Unfortunately, we do not provide default templates yet (but we will, soon) and you need to define them manually.

You can configure more options for the routing generation but you can also define each route manually to have it fully configurable. Continue reading to learn more!
