Filters
=======

Here you can find the supported filters. Keep in mind you can very easily define your own!

String
------

Simplest filter type. It can filter in one or multiple fields.

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
* starts with
* ends with
* empty
* not empty
* in
* not in

If you don't want display to user matching possibilites, you can choose one in a configuration. Then only the value input will display. You can achieve it adding options.type parameter:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    username:
                        type: string
                        options:
                            type: contains

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

This filter checks if an amount is in range and in specific currency

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
