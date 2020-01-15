Customizing Templates
=====================

.. note::

    There are two kinds of templates in Sylius. **Shop** and **Admin** ones, plus you can create your own to satisfy your needs.

Why would you customize a template?
-----------------------------------

The most important case for modifying the existing templates is of course **integrating your own layout of the system**.
Sometimes even if you have decided to stay with the default layout provided by Sylius, you need to **slightly modify it to meet your
business requirements**.
You may just need to **add your logo anywhere**.

Methods of templates customizing
--------------------------------

.. warning::

    There are three ways of customizing templates of Sylius:

    The first one is simple **templates overriding** inside of the ``templates/bundles`` directory of your project. Using
    this method you can completely change the content of templates.

    The second method is **templates customization via events**. You are able to listen on these template events,
    and by that add your own blocks without copying and pasting the whole templates. This feature is really useful
    when :doc:`creating Sylius Plugins </book/plugins/creating-plugin>`.

    The third method is **using Sylius themes**. Creating a Sylius theme requires a few more steps than basic template overriding,
    but allows you to have a different design on multiple channels of the same Sylius instance. :doc:`Learn more about themes here </book/themes/themes>`.

.. tip::

    You can browse the full implementation of these examples on `this GitHub Pull Request.
    <https://github.com/Sylius/Customizations/pull/16>`_


How to customize templates by overriding?
-----------------------------------------

.. note::

    How do you know which template you should be overriding?
    Go to the page that you are going to modify, at the bottom in the Symfony toolbar click on the route,
    which will redirect you to the profiler. In the Request Attributes section
    under ``_sylius [ template => ...]`` you can check the path to the current template.


* **Shop** templates: customizing Login Page template:

The default login template is: ``@SyliusShopBundle/login.html.twig``.
In order to override it you need to create your own: ``templates/bundles/SyliusShopBundle/login.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: php

    {% extends '@SyliusShop/layout.html.twig' %}

    {% import '@SyliusUi/Macro/messages.html.twig' as messages %}

    {% block content %}
    <div class="ui column stackable center page grid">
        {% if last_error %}
            {{ messages.error(last_error.messageKey|trans(last_error.messageData, 'security')) }}
        {% endif %}

        {# You can add a headline for instance to see if you are changing things in the correct place. #}
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

Done! If you do not see any changes on the ``/shop/login`` url, clear your cache:

.. code-block:: bash

    $ php bin/console cache:clear

* **Admin** templates: Customization of the Country form view.

The default template for the Country form is: ``SyliusAdminBundle:Country:_form.html.twig``.
In order to override it you need to create your own: ``templates/bundles/SyliusAdminBundle/Country/_form.html.twig``.

Copy the contents of the original template to make your work easier. And then modify it to your needs.

.. code-block:: php

    <div class="ui segment">
        {{ form_errors(form) }}
        {{ form_row(form.code) }}
        {{ form_row(form.enabled) }}
    </div>
    <div class="ui segment">

        {# You can add a headline for instance to see if you are changing things in the correct place. #}
        <h1>My Custom Headline</h1>

        <h4 class="ui dividing header">{{ 'sylius.ui.provinces'|trans }}</h4>
        {{ form_row(form.provinces, {'label': false}) }}
    </div>

Done! If you do not see any changes on the ``/admin/countries/new`` url, clear your cache:

.. code-block:: bash

    $ php bin/console cache:clear

How to customize templates via events?
--------------------------------------

Sylius uses the Events mechanism provided by the `SonataBlockBundle <https://sonata-project.org/bundles/block/master/doc/reference/events.html>`_.

How to locate template events?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The events naming convention uses the routing to the place where we are adding it, but instead of ``_`` we are using ``.``,
followed by a slot name (like ``sylius_admin_customer_show`` route results in the ``sylius.admin.customer.show.slot_name`` events).
The slot name describes where exactly in the template's structure should the event occur, it will be ``before`` or ``after`` certain elements.

Although when the resource name is not just one word (like ``product_variant``) then the underscore stays in the event prefix string.
Then ``sylius_admin_product_variant_create`` route will have the ``sylius.admin.product_variant.create.slot_name`` events.

Let's see how the event is rendered in a default Sylius Admin template. This is the rendering of the event that occurs
on the create action of Resources, at the bottom of the page (after the content of the create form):

.. code-block:: twig

    {# First we are setting the event_prefix based on route as it was mentioned before #}
    {% set event_prefix = metadata.applicationName ~ '.admin.' ~ metadata.name ~ '.create' %}

    {# And then the slot name is appended to the event_prefix #}
    {{ sonata_block_render_event(event_prefix ~ '.after_content', {'resource': resource}) }}

.. note::

    Besides the events that are named based on routing, Sylius also has some other general events: those that will appear
    on every Sylius admin or shop. Examples: ``sylius.shop.layout.slot_name`` or ``sylius.admin.layout.slot_name``.
    They are rendered in the ``layout.html.twig`` views for both Admin and Shop.

.. tip::

    In order to find events in Sylius templates you can simply search for the ``sonata_block_render_event`` phrase in your
    project's directory.

How to use template events for customizations?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

When you have found an event in the place where you want to add some content, here's what you have to do.

Let's assume that you would like to add some content after the header in the Sylius shop views.
You will need to look at the ``SyliusShopBundle/Resources/views/layout.html.twig`` template,
which is the basic layout of Sylius shop, and then in it find the appropriate event.

For the space below the header it will be ``sylius.shop.layout.after_header``.

* Create a Twig template file that will contain what you want to add.

.. code-block:: twig

    {# templates/block.html.twig #}

    <h1> Test Block Title </h1>

* And configure Sylius UI to display it for the chosen event:

.. code-block:: yaml

    # config/packages/sylius_ui.yaml

    sylius_ui:
        events:
            sylius.shop.layout.after_header:
                blocks:
                    my_block_name: 'block.html.twig'

That's it. Your new block should appear in the view.

.. tip::

    Learn more about adding custom Admin JS & CSS in the cookbook :doc:`here </cookbook/frontend/admin-js-and-css>`.

How to use themes for customizations?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can refer to the theme documentation available here:
- :doc:`Themes (The book) </book/themes/themes>`
- :doc:`SyliusThemeBundle (Bundle documentation) </components_and_bundles/bundles/SyliusThemeBundle/index>`

Global Twig variables
---------------------

Each of the Twig templates in Sylius is provided with the ``sylius`` variable,
that comes from the `ShopperContext <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Core/Context/ShopperContext.php>`_.

The **ShopperContext** is composed of ``ChannelContext``, ``CurrencyContext``, ``LocaleContext`` and ``CustomerContext``.
Therefore it has access to the current channel, currency, locale and customer.

The variables available in Twig are:

+---------------------+----------------------------+
| Twig variable       | ShopperContext method name |
+=====================+============================+
| sylius.channel      | getChannel()               |
+---------------------+----------------------------+
| sylius.currencyCode | getCurrencyCode()          |
+---------------------+----------------------------+
| sylius.localeCode   | getLocaleCode()            |
+---------------------+----------------------------+
| sylius.customer     | getCustomer()              |
+---------------------+----------------------------+

How to use these Twig variables?
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can check for example what is the current channel by dumping the ``sylius.channel`` variable.

.. code-block:: twig

    {{ dump(sylius.channel) }}

That's it, this will dump the content of the current Channel object.

.. include:: /customization/plugins.rst.inc
