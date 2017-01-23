.. index::
   single: Themes

Themes
======

Theming is a method of customizing how your channels look like in Sylius. Each channel can have a different tqheme.

How to enable themes in a project?
----------------------------------

To use themes inside of your project you need to add these few lines to your ``app/config/config.yml``.

.. code-block:: yaml

   sylius_theme:
       sources:
           filesystem: ~

How to create themes?
---------------------

Let's see how to customize the login view inside of your custom theme.

1. Inside of the ``app/themes/`` directory create a new directory for your theme:

Let it be ``CrimsonTheme/`` for instance.

2. Create ``composer.json`` for your theme:

.. code-block:: yaml

   {
       "name": "acme/crimson-theme",
       "authors": [
           {
               "name": "James Potter",
               "email": "prongs@example.com"
           }
       ],
       "extra": {
           "sylius-theme": {
               "title": "Crimson Theme"
           }
       }
   }

3. Customize a template:

In order to customize the login view you should take the content of ``@SyliusShopBundle/views/login.html.twig`` file
and paste it to your theme directory: ``app/themes/CrimsonTheme/SyliusShopBundle/views/login.html.twig``

Let's remove the registration column in this example:

.. code-block:: twig

   {% extends '@SyliusShop/layout.html.twig' %}

   {% form_theme form 'SyliusUiBundle:Form:theme.html.twig' %}

   {% import 'SyliusUiBundle:Macro:messages.html.twig' as messages %}

   {% block content %}
       {% include '@SyliusShop/Login/_header.html.twig' %}
       <div class="ui padded segment">
           <div class="ui one column very relaxed stackable grid">
               <div class="column">
                   <h4 class="ui dividing header">{{ 'sylius.ui.registered_customers'|trans }}</h4>
                   <p>{{ 'sylius.ui.if_you_have_an_account_sign_in_with_your_email_address'|trans }}.</p>
                   {{ form_start(form, {'action': path('sylius_shop_login_check'), 'attr': {'class': 'ui loadable form', 'novalidate': 'novalidate'}}) }}
                       {% include '@SyliusShop/Login/_form.html.twig' %}
                       <button type="submit" class="ui blue submit button">{{ 'sylius.ui.login'|trans }}</button>
                       <a href="{{ path('sylius_shop_request_password_reset_token') }}" class="ui right floated button">{{ 'sylius.ui.forgot_password'|trans }}</a>
                   {{ form_end(form, {'render_rest': false}) }}
               </div>
           </div>
       </div>
   {% endblock %}

.. tip::

   Learn more about customizing templates :doc:`here </customization/template>`.

4. Choose your new theme on the channel:

In the administration panel go to channels and change the theme of your desired channel to ``Crimson Theme``.

.. image:: ../../_images/channel_theme.png
   :align: center

5. If changes are not yet visible, clear the cache:

.. code-block:: bash

   $ php bin/console cache:clear

.. note::

   You can override any template of Sylius like that, as well as static files by adding a ``web/assets/`` directory
   in the theme directory to override CSS or JS files.

Learn more
----------

* :doc:`Theme - Bundle Documentation </bundles/SyliusThemeBundle/index>`.
