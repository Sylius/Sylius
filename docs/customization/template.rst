Customizing Templates
=====================

.. note::

    There are two kinds of templates in Sylius. **Shop** and **Admin** ones, plus you can create your own to satisfy your needs.

Why would you customize a template?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The most important case for modifying the existing templates is of course **integrating your own layout of the system**.
Sometimes even if you have decided to stay with the default layout provided by Sylius, you need to **slightly modify it to meet your
business requirements**.
You may just need to **add your logo anywhere**.

How to customize templates?
~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    How do you know which template you should be overriding?
    Go to the page that you are going to modify, at the bottom in the Symfony toolbar click on the route,
    which will redirect you to the profiler. In the Request Attributes section
    under ``_sylius [ template => ...]`` you can check the path to the current template.


* **Shop** templates: customizing Login Page template:

The default login template is: ``SyliusShopBundle:Account:login.html.twig``.
In order to override it you need to create your own: ``app/Resources/SyliusShopBundle/views/Account/login.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: php

    {% extends 'SyliusShopBundle:Layout:main.html.twig' %}

    {% import 'SyliusUiBundle:Macro:messages.html.twig' as messages %}

    {% block content %}
    <div class="ui column stackable center page grid">
        {% if last_error %}
            {{ messages.error(last_error.messageKey|trans(last_error.messageData, 'security')) }}
        {% endif %}

        // You can add a headline for instance to see if you are changing things in the correct place.
        <h1>
            This Is My Headline
        </h1>

        <div class="five wide column"></div>
        <form class="ui six wide column form segment" action="{{ path('sylius_shop_login_check') }}" method="post" novalidate>
            <div class="one field">
                {{ form_row(form._username, {'value': last_username|default('')}) }}
            </div>
            <div class="one field">
                {{ form_row(form._password) }}
            </div>
            <div class="one field">
                <button type="submit" class="ui fluid large primary submit button">{{ 'sylius.ui.login_button'|trans }}</button>
            </div>
        </form>
    </div>
    {% endblock %}

Done! If you do not see any changes on the ``/shop/login`` url, clear your cache: ``$ php app/console cache:clear``.

* **Admin** templates: Customization of the Country form view.

The default template for the Country form is: ``SyliusAdminBundle:Country:_form.html.twig``.
In order to override it you need to create your own: ``app/Resources/SyliusAdminBundle/views/Country/_form.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: php

    <div class="ui segment">
        {{ form_errors(form) }}
        {{ form_row(form.code) }}
        {{ form_row(form.enabled) }}
    </div>
    <div class="ui segment">

        // You can add a headline for instance to see if you are changing things in the correct place.
        <h1>My Custom Headline</h1>

        <h4 class="ui dividing header">{{ 'sylius.ui.provinces'|trans }}</h4>
        {{ form_row(form.provinces, {'label': false}) }}
    </div>

Done! If you do not see any changes on the ``/admin/countries/new`` url, clear your cache: ``$ php app/console cache:clear``.
