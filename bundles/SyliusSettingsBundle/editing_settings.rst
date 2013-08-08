Editing the settings
====================

To edit the settings via the web interface, simply point users to the ``sylius_settings_update`` route with proper parameters.

In order to update our meta settings, generate the following link.

.. code-block:: html

    <a href="{{ path('sylius_settings_update', {'namespace': 'meta'}) }}">Edit SEO</a>

A proper form will be generated, with a submit action, which updates the settings in database.
