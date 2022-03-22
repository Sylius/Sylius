
Customizing Grids
=================

.. note::

    We assume that you are familiar with grids. If not check the documentation of the `GridBundle <https://github.com/Sylius/SyliusGridBundle/blob/master/docs/index.md>`_ first.

.. tip::

    You can browse the full implementation of these examples on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/19>`_

Why would you customize grids?
------------------------------

When you would like to change how the index view of an entity looks like in the administration panel,
then you have to override its grid.

* remove a field from a grid
* change a field of a grid
* reorder fields
* override an entire grid

How to customize grids?
-----------------------

.. tip::

    One way to change anything in any grid in **Sylius** is to modify a special file in the ``config/packages/`` directory: ``config/packages/_sylius.yaml``.

How to customize fields of a grid?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

How to remove a field from a grid?
''''''''''''''''''''''''''''''''''

If you would like to remove a field from an existing Sylius grid, you will need to disable it in the ``config/packages/_sylius.yaml``.

Let's imagine that we would like to hide the **title of product review** field on the ``sylius_admin_product_review`` grid.

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                fields:
                    title:
                        enabled: false

That's all. Now the ``title`` field will be disabled (invisible).

How to modify a field of a grid?
''''''''''''''''''''''''''''''''

If you would like to modify for instance a label of any field from a grid, that's what you need to do:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                fields:
                    date:
                        label: "When was it added?"

Good practices is translate labels, look :doc:`here </customization/grid>`. how to do that

How to customize filters of a grid?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

How to remove a filter from a grid?
'''''''''''''''''''''''''''''''''''

If you would like to remove a filter from an existing Sylius grid, you will need to disable it in the ``config/packages/_sylius.yaml``.

Let's imagine that we would like to hide the **titles filter of product reviews** on the ``sylius_admin_product_review`` grid.

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                filters:
                    title:
                        enabled: false

That's all. Now the ``title`` filter will be disabled.

How to customize actions of a grid?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

How to remove an action from a grid?
''''''''''''''''''''''''''''''''''''

If you would like to disable some actions in any grid, you just need to set its ``enabled`` option to ``false`` like below:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                actions:
                    item:
                        delete:
                            type: delete
                            enabled: false

How to modify an action of a grid?
''''''''''''''''''''''''''''''''''

If you would like to change the link to which an action button is redirecting, this is what you have to do:

.. warning::

    The ``show`` button does not exist in the ``sylius_admin_product`` grid by default.
    It is assumed that you already have it customized, and your grid has the ``show`` action.

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product:
                actions:
                    item:
                        show:
                            type: show
                            label: Show in the shop
                            options:
                                link:
                                    route: sylius_shop_product_show
                                    parameters:
                                        slug: resource.slug

The above grid modification will change the redirect of the ``show`` action to redirect to the shop, instead of admin show.
Also the label was changed here.

How to remove label of an action from a grid?
'''''''''''''''''''''''''''''''''''''''''''''

If you would like to remove label for some actions in any grid, you just need to set its ``labeled`` option to ``false`` like below:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_product_review:
                actions:
                    item:
                        delete:
                            type: delete
                            options:
                                labeled: false

How to modify positions of fields, filters and actions in a grid?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

For fields, filters and actions it is possible to easily change the order in which they are displayed in the grid.

See an example of fields order modification on the ``sylius_admin_product_review`` grid below:

.. code-block:: yaml

    # config/packages/_sylius.yaml
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

Customizing grids by events
---------------------------

There is also another way to customize grids: **via events**.
Every grid configuration dispatches an event when its definition is being converted.

For example, **sylius_admin_product** grid dispatches such an event:

.. code-block:: yaml

    sylius.grid.admin_product # For the grid of products in admin

To show you an example of a grid customization using events, we will modify fields from a grid using that method.
Here are the steps, that you need to take:

**1.** In order to modify fields from the product grid in **Sylius** you have to create a ``App\Grid\AdminProductsGridListener`` class.

In the example below we are removing the ``image`` field and adding the ``code`` field to the ``sylius_admin_product`` grid.

.. code-block:: php

    <?php

    namespace App\Grid;

    use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
    use Sylius\Component\Grid\Definition\Field;

    final class AdminProductsGridListener
    {
        public function editFields(GridDefinitionConverterEvent $event): void
        {
            $grid = $event->getGrid();

            // Remove
            $grid->removeField('image');

            // Add
            $codeField = Field::fromNameAndType('code', 'string');
            $codeField->setLabel('Code');
            // ...
            $grid->addField($codeField);
        }
    }

**2.** After creating your class with a proper method for the grid customizations you need, subscribe your
listener to the ``sylius.grid.admin_product`` event in the ``config/services.yaml``.

.. code-block:: yaml

    # config/services.yaml
    services:
        App\Grid\AdminProductsGridListener:
            tags:
                - { name: kernel.event_listener, event: sylius.grid.admin_product, method: editFields }

**3.** Result:

After these two steps your admin product grid should not have the image field.

.. include:: /customization/plugins.rst

Changes related to upgrade to GridBundle 1.10
---------------------------------------------

Since the SyliusGridBundle v1.10, all grids has options `fetch_join_collection` and `use_output_walkers` enabled by default.
According to our research it may fix a lot of pagination issues and improve big-database performance (1M+ rows) up to 70%,
but with the price of more queries required to be performed. If this trade-off is not worth it for you,
you may disable it by using the following configuration snippet:

.. code-block:: yaml

    # config/packages/_sylius.yaml
    sylius_grid:
        grids:
            sylius_admin_address_log_entry:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_admin_user:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_channel:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_country:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_currency:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_customer:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_customer_group:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_customer_order:
                extends: sylius_admin_order
                driver:
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_exchange_rate:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_inventory:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_locale:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_order:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_payment:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_payment_method:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product_association_type:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product_attribute:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product_option:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product_review:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_product_variant:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_promotion:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_promotion_coupon:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_shipment:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_shipping_category:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_shipping_method:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_tax_category:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_tax_rate:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_taxon:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_admin_zone:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_shop_account_order:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false
            sylius_shop_product:
                driver:
                    name: doctrine/orm
                    options:
                        pagination:
                            fetch_join_collection: false
                            use_output_walkers: false

Learn more
----------

* `GridBundle documentation <https://github.com/Sylius/SyliusGridBundle/blob/master/docs/index.md>`_
