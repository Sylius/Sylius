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

Boolean
-------

This filter checks if a value is true or false.
