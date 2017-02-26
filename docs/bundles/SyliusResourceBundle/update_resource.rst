Updating Resources
==================

To display an edit form of a particular resource, change it or update it via API, you should use the **updateAction** action of your **app.controller.book** service.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction

Done! Now when you go to ``/books/5/edit``, ResourceController will use the repository (``app.repository.book``) to find the book with id == **5**.
If found it will create the ``app_book`` form, and set the existing book as data.

Submitting the Form
-------------------

You can use exactly the same route to handle the submit of the form and updating the book.

.. code-block:: html

    <form method="post" action="{{ path('app_book_update', {'id': book.id}) }}">
        <input type="hidden" name="_method" value="PUT" />

On submit, the update action with method PUT, will bind the request on the form, and if it is valid it will use the right manager to persist the resource.
Then, by default it redirects to ``app_book_show`` to display the updated book, but like for creation of the resource - it's customizable.

When validation fails, it will simply render the form again, but with error messages.

Changing the Template
---------------------

Just like for other actions, you can customize the template.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                template: Admin/Book/update.html.twig

Using Custom Form
-----------------

Same way like for **createAction** you can override the default form.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                form: AppBundle\Form\BookType

Passing Custom Options to Form
------------------------------

Same way like for **createAction** you can pass options to the form.

Below you can see how to specify custom options, in this case, ``validation_groups``, but you can pass any option accepted by the form.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                form:
                    type: app_book_custom
                    validation_groups: [sylius, my_custom_group]

Overriding the Criteria
-----------------------

By default, the **updateAction** will look for the resource by id. You can easily change that criteria.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{title}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                criteria: { title: $title }

Custom Redirect After Success
-----------------------------

By default the controller will try to get the id of resource and redirect to the "show" route. To change that, use the following configuration.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                redirect: app_book_index

You can also perform more complex redirects, with parameters. For example:

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /genre/{genreId}/books/{id}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                redirect:
                    route: app_genre_show
                    parameters: { id: $genreId }

[API] Returning resource or no content
--------------------------------------

Depending on your app approach it can be useful to return a changed object or only the ``204 HTTP Code``, which indicates that everything worked smoothly.
Sylius, by default is returning the ``204 HTTP Code``, which indicates an empty response. If you would like to receive a whole object as a response you should set a `return_content` option to true.

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /books/{title}/edit
        methods: [GET, PUT]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                criteria: { title: $title }
                return_content: true

.. warning::

    The `return_content` flag is available for the `applyStateMachineTransitionAction` method as well. But these are the only ones which can be configured this way.
    It is worth noticing, that the `applyStateMachineTransitionAction` returns a default `200 HTTP Code` response with a fully serialized object.

Configuration Reference
-----------------------

.. code-block:: yaml

    # app/config/routing.yml

    app_book_update:
        path: /genre/{genreId}/books/{title}/edit
        methods: [GET, PUT, PATCH]
        defaults:
            _controller: app.controller.book:updateAction
            _sylius:
                template: Book/editInGenre.html.twig
                form: app_book_custom
                repository:
                    method: findBookByTitle
                    arguments: [$title, expr:service('app.context.book')]
                criteria:
                    enabled: true
                    genreId: $genreId
                redirect:
                    route: app_book_show
                    parameters: { title: resource.title }
                return_content: true
