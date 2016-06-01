Your First Grid
===============

In order to use grids, we need to register your entity as a Sylius resource. Let us assume you have Tournament model in your application, which represents a sport tournament and has several fields, including name, date, status and category.

In order to make it a Sylius resource, you need to configure it under ``sylius_resource`` node:

.. code-block:: yaml

    # app/config/config.yml

    sylius_resource:
        resources:
            app.tournament:
                driver: doctrine/orm # Use appropriate driver.
                classes:
                    model: AppBundle\Entity\Tournament

That's it! Your class is now a resource. In order to learn what does it mean, please refer to :doc:`SyliusResourceBundle </bundles/SyliusResourceBundle/index>` documentation.

Columns Definition
------------------

Now we can configure our first grid:

.. code-block:: yaml

    # app/config/config.yml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm # Use appropriate driver.
                resource: app.tournament
                columns:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: string
                        options:
                            path: category.name

Generating The CRUD Routing
---------------------------

That's it. SyliusResourceBundle allows to generate a default CRUD interface including the grid we have just defined. Just put this in your routing configuration!

.. code-block:: yaml

    # app/config/routing.yml

    app_tournament:
        resource: app.tournament
        type: sylius.resource

This will generate the following paths:

 * GET */tournaments/* - Your grid.
 * GET/POST */tournaments/new* - Creating new tournament.
 * GET/PUT */tournaments/{id}/edit* - Editing an existing tournament.
 * DELETE */tournaments/{id}* - Deleting specific tournament.
 * GET */tournaments/{id}* - Displaying specific tournament.

Defining Filters
----------------

Okay, but we need some filters, right?

.. code-block:: yaml

    # app/config/config.yml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm # Use appropriate driver.
                resource: app.tournament
                columns:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: string
                        options:
                            path: category.name
                filters:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: entity
                        options:
                            entity: AppBundle:TournamentCategory

Default Sorting
---------------

We want to have our tournaments sorted by name, by default, right? That is easy!

.. code-block:: yaml

    # app/config/config.yml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm # Use appropriate driver.
                resource: app.tournament
                sorting:
                    name: asc
                columns:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: string
                        options:
                            path: category.name
                filters:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: entity
                        options:
                            entity: AppBundle:TournamentCategory

Actions Configuration
---------------------

Next step is adding some actions to the grid. We start with the basic ones, edit and delete. We can also add a simple custom action with external link.


.. code-block:: yaml

    # app/config/config.yml

    sylius_grid:
        grids:
            app_tournament:
                driver: doctrine/orm # Use appropriate driver.
                resource: app.tournament
                sorting:
                    name: asc
                columns:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: string
                        options:
                            path: category.name
                filters:
                    name:
                        type: string
                    date:
                        type: datetime
                    status:
                        type: boolean
                    category:
                        type: entity
                        options:
                            entity: AppBundle:TournamentCategory
                actions:
                    edit:
                        type: link
                        options:
                            route: app_tournament_update
                    delete:
                        type: submit
                        options:
                            route: app_tournament_delete
                            method: DELETE

Your grid is ready to use!
