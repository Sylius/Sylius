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

How to remove a filter from grid?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to remove a filter for an existing Sylius grid, you will need to disable it in the `app/config/grids.yml`.

Let's imagine that we would like to hide the **titles filter of product reviews** on the `sylius_admin_product_review` grid.

.. code-block:: yaml

    # app/config/grids.yml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                filters:
                    title:
                        enabled: false

That's all. Now the `title` filter will be disabled.

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

How to modify positions of fields, filters and actions in grid?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For fields, filters and actions it is possible to easily change the order in which they are displayed in the grid.

See an example of fields order modification on the `sylius_admin_product_review` grid below:

.. code-block:: yaml

    # app/config/grids.yml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                fields:
                    date:
                        position: 5
                    title:
                        position: 6
                    rating:
                        position: 3
                    status:
                        position: 1
                    reviewSubject:
                        position: 2
                    author:
                        position: 4

Customize grids by events
-------------------------

There is also another way to customize grids: **via events**.
Every grid configuration dispatches an event when its definition is being converted.

For example, **sylius_admin_product** grid dispatches such an event:

.. code-block:: php

    sylius.grid.admin_product # For the grid of products in admin

To show you an example of a grid customization using events, we will remove a field from a grid using that method.
Here are the steps, that you need to take:

**1.** In order to remove fields from the product grid in **Sylius** you have to create a ``AppBundle\Grid\AdminProductsGridListener`` class.

In the example below we are removing the ``images`` field from the ``sylius_admin_product`` grid.

.. code-block:: php

    <?php

    namespace AppBundle\Grid;

    use Sylius\Bundle\GridBundle\Event\GridDefinitionConverterEvent;

    final class AdminProductsGridListener
    {
        /**
         * @param GridDefinitionConverterEvent $event
         */
        public function removeImageField(GridDefinitionConverterEvent $event)
        {
            $grid = $event->getGrid();

            $grid->removeField('image');
        }
    }

**2.** After creating your class with a proper method for the grid customizations you need, subscribe your
listener to the ``sylius.grid.admin_product`` event in the ``app/config/services.yml``.

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.listener.admin.products_grid:
            class: AppBundle\Grid\AdminProductsGridListener
            tags:
                - { name: kernel.event_listener, event: sylius.grid.admin_product, method: removeImageField }

Remember to import the ``app/config/services.yml`` into the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "services.yml" }

**3.** Result:

After these two steps your admin product grid should not have the image field.

Learn more
----------

* :doc:`Grid - Component Documentation </components/Grid/index>`
* :doc:`Grid - Bundle Documentation </bundles/SyliusGridBundle/index>`
