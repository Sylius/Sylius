Filters
=======

**Filters** on grids are a kind of search prepared for each grid. Having a grid of objects you can filter out only those
with a specified name, or value etc.
Here you can find the supported filters. Keep in mind you can very easily define your own ones!

String
------

Simplest filter type. It can filter by one or multiple fields.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    username:
                        type: string
                    email:
                        type: string
                    firstName:
                        type: string
                    lastName:
                        type: string

The filter allows the user to select following search options:

* contains
* not contains
* equal
* not equal
* starts with
* ends with
* empty
* not empty
* in
* not in

If you don't want display all theses matching possibilities, you can choose just one of them.
Then only the input field will be displayed. You can achieve it like that:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    username:
                        type: string
                        form_options:
                            type: contains

By configuring a filter like above you will have only an input field for filtering users objects that ``contain`` a given string in their username.

Boolean
-------

This filter checks if a value is true or false.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_channel:
                filters:
                    enabled:
                        type: boolean

Date
----

This filter checks if a chosen datetime field is between given dates.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_order:
                filters:
                    createdAt:
                        type: date
                    completedAt:
                        type: date

Entity
------

This type filters by a chosen entity.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_order:
                filters:
                    channel:
                        type: entity
                        form_options:
                            class: "%app.model.channel%"
                    customer:
                        type: entity
                        form_options:
                            class: "%app.model.customer%"

Money
_____

This filter checks if an amount is in range and in a specified currency

.. code-block:: yaml

    sylius_grid:
        grids:
            app_order:
                filters:
                    total:
                        type: money
                        form_options:
                            scale: 3
                        options:
                            currency_field: currencyCode
                            scale: 3

.. warning::

    Providing different ``scale`` between **form_options** and **options** may cause unwanted, and plausibly volatile results.

Exists
------

This filter checks if the specified field contains any value

.. code-block:: yaml

    sylius_grid:
        grids:
            app_order:
                filters:
                    date:
                        type: exists
                        options:
                            field: completedAt

Custom Filters
--------------

.. tip::

    If you need to create a custom filter, :doc:`read the docs here </bundles/SyliusGridBundle/custom_filter>`.
