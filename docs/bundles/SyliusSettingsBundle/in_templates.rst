Using in templates
==================

Bundle provides handy **SyliusSettingsExtension** which you can use in your templates.

In our example, it can be something like:

.. code-block:: jinja

    {% set meta = sylius_settings_all('meta') %}

    <head>
        <title>{{ meta.title }}</title>
        <meta name="keywords" content="{{ meta.meta_keywords }}">
        <meta name="description" content="{{ meta.meta_description }}">
    </head>

There is also `sylius_settings_get()` to get particular setting directly.

.. code-block:: jinja

    {% set title = sylius_settings_get('meta.title') %}

    <head>
        <title>{{ title }}</title>
    </head>
