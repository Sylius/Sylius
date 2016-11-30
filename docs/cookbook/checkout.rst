How to customize Sylius Checkout?
=================================

Why would you override the Checkout process?
--------------------------------------------

This is a common problem for many Sylius users. Sometimes the checkout process we have designed is not suitable for your custom business needs.
Therefore you need to learn how to modify it, when you will need to for example:

* remove shipping step - when you do not ship the products you sell,
* change the order of checkout steps,
* merge shipping and addressing step into one common step,
* or even make the whole checkout a one page process.

See how to do these things below:

How to remove a step from checkout?
-----------------------------------

Let's imagine that you are trying to create a shop that does not need shipping - it sells downloadable files only.

To meet your needs you will need to adjust checkout process. **What do you have to do then?** See below:

Overwrite the state machine of Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open the `CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml>`_
and place its content in the ``app/Resources/SyliusCoreBundle/config/app/state_machine/sylius_order_checkout.yml``
which is a `standard procedure of overriding configs in Symfony <http://symfony.com/doc/current/bundles/inheritance.html#overriding-resources-templates-routing-etc>`_.
Remove the ``shipment_selected`` state, ``select_shipment`` transition. Remove the ``select_shipment`` from the
``sylius_process_cart`` callback.

.. code-block:: yaml

    # app/Resources/SyliusCoreBundle/config/app/state_machine/sylius_order_checkout.yml
    winzou_state_machine:
        sylius_order_checkout:
            class: "%sylius.model.order.class%"
            property_path: checkoutState
            graph: sylius_order_checkout
            state_machine_class: "%sylius.state_machine.class%"
            states:
                cart: ~
                addressed: ~
                payment_selected: ~
                completed: ~
            transitions:
                address:
                    from: [cart, addressed, payment_selected]
                    to: addressed
                select_payment:
                    from: [addressed, payment_selected]
                    to: payment_selected
                complete:
                    from: [payment_selected]
                    to: completed
            callbacks:
                after:
                    sylius_process_cart:
                        on: ["address", "select_payment"]
                        do: ["@sylius.order_processing.order_processor", "process"]
                        args: ["object"]
                    sylius_create_order:
                        on: ["complete"]
                        do: ["@sm.callback.cascade_transition", "apply"]
                        args: ["object", "event", "'create'", "'sylius_order'"]
                    sylius_hold_inventory:
                        on: ["complete"]
                        do: ["@sylius.inventory.order_inventory_operator", "hold"]
                        args: ["object"]
                    sylius_assign_token:
                        on: ["complete"]
                        do: ["@sylius.unique_id_based_order_token_assigner", "assignTokenValue"]
                        args: ["object"]
                    sylius_increment_promotions_usages:
                        on: ["complete"]
                        do: ["@sylius.promotion_usage_modifier", "increment"]
                        args: ["object"]

.. tip::

    To check if your new state machine configuration is overiding the old one run:
    ``$ php app/console debug:config winzou_state_machine`` and check the configuration of ``sylius_order_checkout``.

Adjust Checkout Resolver
~~~~~~~~~~~~~~~~~~~~~~~~

The next step of customizing Checkout is to adjust the Checkout Resolver to match the changes you have made in the state machine.
Make these changes in the ``config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    sylius_shop:
        checkout_resolver:
            route_map:
                cart:
                    route: sylius_shop_checkout_address
                addressed:
                    route: sylius_shop_checkout_select_payment
                payment_selected:
                    route: sylius_shop_checkout_complete

Adjust Checkout Templates
~~~~~~~~~~~~~~~~~~~~~~~~~

After you have got the resolver adjusted, modify the templates for checkout. You have to remove shipping from steps and
disable the hardcoded ability to go back to the shipping step. You will achieve that by overriding two files:

* `ShopBundle/Resources/views/Checkout/_steps.html.twig <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/views/Checkout/_steps.html.twig>`_
* `ShopBundle/Resources/views/Checkout/SelectPayment/_form.html.twig <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/views/Checkout/SelectPayment/_form.html.twig>`_

.. code-block:: html

    {# app/Resources/SyliusShopBundle/views/Checkout/_steps.html.twig #}
    {% if active is not defined or active == 'address' %}
        {% set steps = {'address': 'active', 'select_payment': 'disabled', 'complete': 'disabled'} %}
    {% elseif active == 'select_payment' %}
        {% set steps = {'address': 'completed', 'select_payment': 'active', 'complete': 'disabled'} %}
    {% else %}
        {% set steps = {'address': 'completed', 'select_payment': 'completed', 'complete': 'active'} %}
    {% endif %}

    <div class="ui three steps">
        <a class="{{ steps['address'] }} step" href="{{ path('sylius_shop_checkout_address') }}">
            <i class="map icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.address'|trans }}</div>
                <div class="description">{{ 'sylius.ui.fill_in_your_billing_and_shipping_addresses'|trans }}</div>
            </div>
        </a>
        <a class="{{ steps['select_payment'] }} step" href="{{ path('sylius_shop_checkout_select_payment') }}">
            <i class="payment icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.payment'|trans }}</div>
                <div class="description">{{ 'sylius.ui.choose_how_you_will_pay'|trans }}</div>
            </div>
        </a>
        <div class="{{ steps['complete'] }} step" href="{{ path('sylius_shop_checkout_complete') }}">
            <i class="checkered flag icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.complete'|trans }}</div>
                <div class="description">{{ 'sylius.ui.review_and_confirm_your_order'|trans }}</div>
            </div>
        </div>
    </div>

.. code-block:: html

    {# app/Resources/SyliusShopBundle/views/Checkout/SelectPayment/_form.html.twig #}
    <div class="ui unmargined segments">
        {% set disabled = false %}
        {% for payment in order.payments %}
            <div class="ui segment">
                <div class="ui dividing header">{{ 'sylius.ui.payment'|trans }} #{{ loop.index }}</div>
                <div class="ui fluid stackable items">
                    {% set payment_form = form.payments[loop.index0] %}
                    {{ form_errors(payment_form.method) }}
                    {% for payment_method_choice in payment_form.method %}
                        <div class="item">
                            <div class="field">
                                <div class="ui radio checkbox">
                                    {{ form_widget(payment_method_choice) }}
                                </div>
                            </div>
                            <div class="content">
                                <a class="header">{{ form_label(payment_method_choice) }}</a>
                                {% if payment_method_choice.parent.vars.choices[loop.index0].data.description is not null %}
                                    <div class="description">
                                        <p>{{ payment_method_choice.parent.vars.choices[loop.index0].data.description }}</p>
                                    </div>
                                {% endif %}
                            </div>
                        </div>
                    {% else %}
                        {% set disabled = true %}
                        {% include '@SyliusShop/Checkout/SelectPayment/_no_payment_methods_available.twig' %}
                    {% endfor %}
                </div>
            </div>
        {% else %}
            {% set disabled = true %}
            {% include '@SyliusShop/Checkout/SelectPayment/_no_payment_methods_available.twig' %}
        {% endfor %}
    </div>
    <div class="ui hidden divider"></div>
    <div class="ui two column grid">
        <div class="column">
            <a href="{{ path('sylius_shop_checkout_address') }}" class="ui large icon labeled button"><i class="arrow left icon"></i> {{ 'sylius.ui.change_address'|trans }}</a>
        </div>
        <div class="right aligned column">
            <button type="submit" id="next-step" class="ui large primary icon labeled {% if disabled %} disabled {% endif %} button">
                <i class="arrow right icon"></i>
                {{ 'sylius.ui.next'|trans }}
            </button>
        </div>
    </div>

Overwrite routing for Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Unfortunately there is no better way - you have to overwrite the whole routing for Checkout.
To do that copy the content of `ShopBundle/Resources/config/routing/checkout.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/config/routing/checkout.yml>`_
to the ``app/Resources/SyliusShopBundle/config/routing/checkout.yml`` file. **Remove routing** of ``sylius_shop_checkout_select_shipping``
and change the **redirect route** in ``sylius_shop_checkout_address``. The rest should remain the same.

.. code-block:: yaml

    # app/Resources/SyliusShopBundle/config/routing/checkout.yml
    sylius_shop_checkout_start:
        path: /
        defaults:
            _controller: FrameworkBundle:Redirect:redirect
            route: sylius_shop_checkout_address

    sylius_shop_checkout_address:
        path: /address
        methods: [GET, PUT]
        defaults:
            _controller: sylius.controller.order:updateAction
            _sylius:
                event: address
                flash: false
                template: SyliusShopBundle:Checkout:address.html.twig
                form:
                    type: sylius_checkout_address
                    options:
                        customer: expr:service('sylius.context.customer').getCustomer()
                repository:
                    method: find
                    arguments: [expr:service('sylius.context.cart').getCart()]
                state_machine:
                    graph: sylius_order_checkout
                    transition: address
                redirect:
                    route: sylius_shop_checkout_select_payment
                    parameters: []

    sylius_shop_checkout_select_payment:
        path: /select-payment
        methods: [GET, PUT]
        defaults:
            _controller: sylius.controller.order:updateAction
            _sylius:
                event: payment
                flash: false
                template: SyliusShopBundle:Checkout:selectPayment.html.twig
                form: sylius_checkout_select_payment
                repository:
                    method: find
                    arguments: [expr:service('sylius.context.cart').getCart()]
                state_machine:
                    graph: sylius_order_checkout
                    transition: select_payment
                redirect:
                    route: sylius_shop_checkout_complete
                    parameters: []

    sylius_shop_checkout_complete:
        path: /complete
        methods: [GET, PUT]
        defaults:
            _controller: sylius.controller.order:updateAction
            _sylius:
                event: summary
                flash: false
                template: SyliusShopBundle:Checkout:complete.html.twig
                repository:
                    method: find
                    arguments: [expr:service('sylius.context.cart').getCart()]
                state_machine:
                    graph: sylius_order_checkout
                    transition: complete
                redirect:
                    route: sylius_shop_order_pay
                    parameters:
                        paymentId: expr:service('sylius.context.cart').getCart().getLastNewPayment().getId()
                form:
                    type: sylius_checkout_complete
                    options:
                        validation_groups: 'sylius_checkout_complete'

.. tip::

    If you do not see any changes run ``$ php app/console cache:clear``.

Learn more
----------

* :doc:`Checkout - concept Documentation </book/checkout>`
* :doc:`State Machine - concept Documentation </book/state_machine>`
* :doc:`Customization Guide </customization/index>`
