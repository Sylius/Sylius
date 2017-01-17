Field Types
===========

This is the list of built-in field types.

String
------

Simplest column type, which basically renders the value at given path as a string.

By default is uses the name of the field, but your can specify the path alternatively. For example:

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                fields:
                    email:
                        type: string
                        label: app.ui.email # each filed type can have a label, we suggest using translation keys instead of messages
                        path: contactDetails.email

This configuration will display the value from ``$user->getContactDetails()->getEmail()``.

DateTime
--------

This column type works exactly the same way as *string*, but expects *DateTime* instance and outputs a formatted date and time string.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                fields:
                    birthday:
                        type: datetime
                        label: app.ui.birthday
                        options:
                            format: 'Y:m:d H:i:s' # this is the default value, but you can modify it

Twig (*twig*)
-------------

Twig column type is the most flexible from all of them, because it delegates the logic of rendering the value to Twig templating engine.
You just have to specify the template and it will be rendered with the ``data`` variable available to you.

.. code-block:: yaml

    sylius_grid:
        grids:
            app_user:
                fields:
                    name:
                        type: twig
                        label: app.ui.name
                        options:
                            template: :Grid/Column:_prettyName.html.twig

In the ``:Grid/Column:_prettyName.html.twig`` template, you just need to render the value for example as you see below:

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
                fields:
                    status:
                        type: boolean
                        label: app.ui.status
