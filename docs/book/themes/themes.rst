.. index::
   single: Themes

Themes
======

Theming is method of customizing how do your channels look like in Sylius. Each channel can have a different Theme.

How to enable themes in a project?
----------------------------------

To use themes inside of your project you need to add these few lines to your ``app/config/config.yml``.

.. code-block:: yaml

   sylius_theme:
       sources:
           filesystem: ~

How to create themes?
---------------------

Let's see how to customize the view of cart widget inside of your custom theme.

1. Inside of the ``app/themes/`` directory create a new directory for your theme:

Let it be ``CrimsonTheme/`` for instance.

2. Create ``composer.json`` for your theme:

.. code-block:: yaml

   {
       "name": "sylius/crimson-theme",
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

In order to customize the cart widget you should take the content of ``@SyliusShopBundle/views/Cart/_widget.html.twig`` file
and paste it to your theme directory: ``app/themes/CrimsonTheme/SyliusShopBundle/views/Cart/_widget.html.twig``

Then change the colour of the widget by simply adding style to it ``style="background-color: crimson"``, and for example
add a heading when the cart is empty.

.. code-block:: twig

   {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

   <div id="sylius-cart-button" class="ui circular cart button">
       <i class="cart icon"></i>
       <span id="sylius-cart-total">
           {{ money.convertAndFormat(cart.itemsTotal) }}
       </span>
       {% transchoice cart.items|length %}sylius.ui.item.choice{% endtranschoice %}
   </div>
   <div class="ui large flowing cart hidden popup" style="background-color: crimson">
       {% if cart.empty %}
           <h1>
               This Is My Headline
           </h1>
           {{ 'sylius.ui.your_cart_is_empty'|trans }}.
       {% else %}
           <div class="ui list">
               {% for item in cart.items %}
                   <div class="item">{{ item.quantity }} x <strong>{{ item.product }}</strong> {{ money.convertAndFormat(item.unitPrice) }}</div>
               {% endfor %}
               <div class="item"><strong>{{ 'sylius.ui.subtotal'|trans }}</strong>: {{ money.convertAndFormat(cart.itemsTotal) }}</div>
           </div>
           <a href="{{ path('sylius_shop_cart_summary') }}" class="ui fluid basic text button">{{ 'sylius.ui.view_and_edit_cart'|trans }}</a>
           <div class="ui divider"></div>
           <a href="{{ path('sylius_shop_checkout_start') }}" class="ui fluid primary button">{{ 'sylius.ui.checkout'|trans }}</a>
       {% endif %}
   </div>

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

   You can override any template of Sylius like that, but also add the ``web/assets/`` inside of the theme directory to override the css or js files.

Learn more
----------

* :doc:`Theme - Bundle Documentation </bundles/SyliusThemeBundle/index>`.
