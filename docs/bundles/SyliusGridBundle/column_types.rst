Column Types
============

This is the list of built-in column types.

String (*string*)
-----------------

Simplest column type, which basically renders the value at given path as a string.

path
    Path to the value

By default is uses the name of the column, but your can specify the path in options. For example:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                columns:
                    email:
                        type: string
                        options:
                            path: contactDetails.email

This configuration will display the value from ``$user->getContactDetails()->getEmail()``.

DateTime (*datetime*)
---------------------

This column type works exactly the same way as *string*, but expects *DateTime* instance and outputs a formatted date and time string.

Date (*date*)
-------------

This column type works exactly the same way as *string*, but expects *DateTime* instance and outputs a formatted date string.

Twig (*twig*)
-------------

Twig column type is the most flexible from all of them, because it delegates the logic of rendering the value to Twig templating engine. You just have to specify the template and it will be rendered with the ``data`` variable available to you.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                columns:
                    name:
                        type: twig
                        options:
                            template: :Grid/Column:_prettyName.html.twig

In the ``:Grid/Column:_prettyName.html.twig`` template, you just need to render the value as you see fit:

.. code-block:: twig

    <strong>{{ data.name }}</strong>
    <p>{{ data.description|markdown }}</p>

Boolean (*boolean*)
-------------------

Boolean column type expects the value to be boolean and renders a default or custom Twig template.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                columns:
                    status:
                        type: boolean
                        options:
                            path: accountDetails.enabled # Optional!
                            template: :Grid/Column:_userStatus.html.twig # Optional!

Array (*array*)
---------------

This column type is a list of values. If you do not specify the Twig template, it will simply display the values as comma separated list. Otherwise it will render the value using Twig.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                columns:
                    groups:
                        type: array
                        options:
                            template: :Grid/Column:_userGroups.html.twig # Optional!
