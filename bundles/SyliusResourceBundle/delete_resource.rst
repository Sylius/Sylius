Deleting resource
=================

Deleting resource is simple.

.. code-block:: yaml

    # routing.yml

    app_user_delete:
        pattern: /users/{id}
        methods: [DELETE]
        defaults:
            _controller: app.controller.user:deleteAction

Calling the action with method DELETE
-------------------------------------

Currently browsers do not support "DELETE" http method. Fortunately, Symfony has very useful feature. You can make a POST call with override parameter, which will
force the framework to treat the request as specified method.

.. code-block:: html

    <form method="post" action="{{ path('app_user_delete', {'id': user.id}) }}">
        <input type="hidden" name="_method" value="DELETE" />

On submit, the delete action with method DELETE, will remove and flush the resource.
Then, by default it redirects to ``app_user_index`` to display the users index, but like for other actions - it's customizable.

Overriding the criteria
-----------------------

By default, **deleteAction** will look for the resource by id. However, you can easily change that.
For example, you want to delete user who belongs to particular company, not only by his id.

.. code-block:: yaml

    # routing.yml

    app_user_delete:
        pattern: /companies/{companyId}/users/{id}
        methods: [DELETE]
        defaults:
            _controller: app.controller.user:deleteAction
            _sylius:
                criteria:
                    id:      $id
                    company: $companyId

There is no magic hacks behind that, it simply takes parameters from request and builds the criteria array for ``findOneBy`` repository method.

Custom redirect after success
-----------------------------

By default the controller will try to get the id of resource and redirect to "index" route. To change that, use following configuration.

.. code-block:: yaml

    # routing.yml

    app_user_delete:
        pattern: /competition/{competitionId}/users/{id}
        methods: [DELETE]
        defaults:
            _controller: app.controller.user:deleteAction
            _sylius:
                redirect:
                    route: app_competition_show
                    parameters: { id: $competitionId }
