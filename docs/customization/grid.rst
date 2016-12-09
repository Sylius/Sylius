Customizing Grids
=================

We assume that you are familiar what grids are. If not check the documentation of the :doc:`Grid Bundle </bundles/SyliusGridBundle/index>`
and :doc:`Grid Component </components/Grid/index>` first.

Why would you customize grids?
------------------------------

When you would like to change how the index view of an entity looks like in the administration panel,
then you have to override its grid.

* remove a field from grid
* change a field of grid
* reorder fields
* override entire grid

How to customize grids?
-----------------------

.. tip::

    First of all if you are attempting to change anything in any state machine in **Sylius** you will need a special file:
    ``app/config/grids.yml`` which has to be imported in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "grids.yml" }


How to remove a field from grid?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to remove a field for an existing Sylius grid, you will need to disable it in the `app/config/grids.yml`.

Let's imagine that we would like to hide the **titles of product reviews** field on the `sylius_admin_product_review` grid.

.. code-block:: yaml

    # app/config/grids.yml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                fields:
                    title:
                        enabled: false

That's all. Now the `title` field will be disabled.

How to modify a field of grid?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to modify for instance a label of any field from grid, that's what you need to do:

.. code-block:: yaml

    # app/config/grids.yml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                fields:
                    date:
                        label: "When was it added?"

How to remove an action from grid?
----------------------------------

If you would like to disable some actions for any grid you just need to set their `enabled` option to `false` like below:

.. code-block:: yaml

    # app/config/grids.yml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                actions:
                    item:
                        delete:
                            type: delete
                            enabled: false

Learn more
----------

* :doc:`Grid - Component Documentation </components/Grid/index>`
* :doc:`Grid - Bundle Documentation </bundles/SyliusGridBundle/index>`
