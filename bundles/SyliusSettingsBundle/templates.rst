Using in templates
==================

Bundle provides handy **SyliusSettingsExtension** which you can use in your templates.

In our example, it can be something like:

.. code-block:: jinja

    {% set metadata = sylius_settings_all('default') %}

    <head>
        <title>{{ metadata.title }}</title>
        <meta name="keywords" content="{{ metadata.meta_keywords }}">
        <meta name="description" content="{{ metadata.meta_description }}">
    </head>

There is also `sylius_settings_get()` to get particular setting directly.
