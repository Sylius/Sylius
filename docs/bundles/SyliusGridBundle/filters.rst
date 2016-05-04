Filters
=======

Here you can find the list of all supported filters. Keep in mind you can very easily define your own!

String (*string*)
-----------------

Simplest filter type. It can filter in one or multiple columns, by default it uses the filter name as field, but you can specify different set of fields.

fields
    Array of fields to filter

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    search:
                        type: string
                        options:
                            fields: [username, e-mail, firstName, lastName]

The filter allows the user to select following search options:

* contains
* not contains
* starts with
* ends with
* empty
* not empty
* equals
* not equals

DateTime (*datetime*)
---------------------

This filter has the following search options:

* between
* not between
* more than
* less than

Date (*date*)
-------------

This filter type works exactly the same way as *datetime*, but does not include the time.

The filter has the following search options:

* between
* not between
* more than
* less than

Boolean (*boolean*)
-------------------

This filter checks if value is true or false.

Entity (*entity*)
-----------------

This is entity filter and allows you to select appropriate entity from list and filter using this value.

class
    Entity name (full or short)
multiple (Default: false)
    Allow to select multiple values?

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    brand:
                        type: entity
                        options:
                            class: AppBundle:Brand
                            multiple: true

Choice (*choice*)
-----------------

This filter allows the user to select one or multiple values and filter the result set.

choices
    Array of choices
multiple (Default: false)
    Allow to select multiple values?

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    gender:
                        type: choice
                        options:
                            choices:
                                male: Boys
                                female: Girls

Country (*country*)
-------------------

This filter allows the user to select one or multiple countries.

multiple (Default: false)
    Allow to select multiple values?

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    from:
                        type: country
                        options:
                            multiple: true

Currency (*currency*)
---------------------

This filter allows the user to select one or multiple currencies.

multiple (Default: false)
    Allow to select multiple values?

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                filters:
                    currency:
                        type: currency
                        options:
                            multiple: true
