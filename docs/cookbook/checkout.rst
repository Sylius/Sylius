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
Remove the ``shipping_selected`` and ``shipping_skipped`` states, ``select_shipping`` and ``skip_shipping`` transitions.
Remove the ``select_shipping`` and ``skip_shipping`` transition from the ``sylius_process_cart`` callback.

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
                payment_skipped: ~
                payment_selected: ~
                completed: ~
            transitions:
                address:
                    from: [cart, addressed, payment_selected, payment_skipped]
                    to: addressed
                skip_payment:
                    from: [addressed]
                    to: payment_skipped
                select_payment:
                    from: [addressed, payment_selected]
                    to: payment_selected
                complete:
                    from: [payment_selected, payment_skipped]
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
                    sylius_save_checkout_completion_date:
                        on: ["complete"]
                        do: ["object", "completeCheckout"]
                        args: ["object"]
                    sylius_skip_shipping:
                        on: ["address"]
                        do: ["@sylius.state_resolver.order_checkout", "resolve"]
                        args: ["object"]
                        priority: 1
                    sylius_skip_payment:
                        on: ["address"]
                        do: ["@sylius.state_resolver.order_checkout", "resolve"]
                        args: ["object"]
                        priority: 1

.. tip::

    To check if your new state machine configuration is overriding the old one run:
    ``$ php bin/console debug:winzou:state-machine`` and check the configuration of ``sylius_order_checkout``.

Adjust Checkout Resolver
~~~~~~~~~~~~~~~~~~~~~~~~

The next step of customizing Checkout is to adjust the Checkout Resolver to match the changes you have made in the state machine.
Make these changes in the ``config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    sylius_shop:
        checkout_resolver:
            pattern: /checkout/.+
            route_map:
                cart:
                    route: sylius_shop_checkout_address
                addressed:
                    route: sylius_shop_checkout_select_payment
                payment_selected:
                    route: sylius_shop_checkout_complete
                payment_skipped:
                    route: sylius_shop_checkout_complete

Adjust Checkout Templates
~~~~~~~~~~~~~~~~~~~~~~~~~

After you have got the resolver adjusted, modify the templates for checkout. You have to remove shipping from steps and
disable the hardcoded ability to go back to the shipping step and the number of steps being displayed in the checkout navigation.
You will achieve that by overriding two files:

* `ShopBundle/Resources/views/Checkout/_steps.html.twig <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/views/Checkout/_steps.html.twig>`_
* `ShopBundle/Resources/views/Checkout/SelectPayment/_navigation.html.twig <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/views/Checkout/SelectPayment/_navigation.html.twig>`_

.. code-block:: html

    {# app/Resources/SyliusShopBundle/views/Checkout/_steps.html.twig #}
    {% if active is not defined or active == 'address' %}
        {% set steps = {'address': 'active', 'select_payment': 'disabled', 'complete': 'disabled'} %}
    {% elseif active == 'select_payment' %}
        {% set steps = {'address': 'completed', 'select_payment': 'active', 'complete': 'disabled'} %}
    {% else %}
        {% set steps = {'address': 'completed', 'select_payment': 'completed', 'complete': 'active'} %}
    {% endif %}

    {% set order_requires_payment = sylius_is_payment_required(order) %}

    {% set steps_count = 'three' %}
    {% if not order_requires_payment %}
        {% set steps_count = 'two' %}
    {% endif %}

    <div class="ui {{ steps_count }} steps">
        <a class="{{ steps['address'] }} step" href="{{ path('sylius_shop_checkout_address') }}">
            <i class="map icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.address'|trans }}</div>
                <div class="description">{{ 'sylius.ui.fill_in_your_billing_and_shipping_addresses'|trans }}</div>
            </div>
        </a>
        {% if order_requires_payment %}
        <a class="{{ steps['select_payment'] }} step" href="{{ path('sylius_shop_checkout_select_payment') }}">
            <i class="payment icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.payment'|trans }}</div>
                <div class="description">{{ 'sylius.ui.choose_how_you_will_pay'|trans }}</div>
            </div>
        </a>
        {% endif %}
        <div class="{{ steps['complete'] }} step" href="{{ path('sylius_shop_checkout_complete') }}">
            <i class="checkered flag icon"></i>
            <div class="content">
                <div class="title">{{ 'sylius.ui.complete'|trans }}</div>
                <div class="description">{{ 'sylius.ui.review_and_confirm_your_order'|trans }}</div>
            </div>
        </div>
    </div>

.. code-block:: html

    {# app/Resources/SyliusShopBundle/views/Checkout/SelectPayment/_navigation.html.twig #}
    {% set enabled = order.payments|length %}

    <div class="ui two column grid">
        <div class="column">
            <a href="{{ path('sylius_shop_checkout_address') }}" class="ui large icon labeled button"><i class="arrow left icon"></i> {{ 'sylius.ui.change_address'|trans }}</a>
        </div>
        <div class="right aligned column">
            <button type="submit" id="next-step" class="ui large primary icon labeled{% if not enabled %} disabled{% endif %} button">
                <i class="arrow right icon"></i>
                {{ 'sylius.ui.next'|trans }}
            </button>
        </div>
    </div>

Overwrite routing for Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Unfortunately there is no better way - you have to overwrite the whole routing for Checkout.
To do that copy the content of
`ShopBundle/Resources/config/routing/checkout.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/config/routing/checkout.yml>`_
to the ``app/Resources/SyliusShopBundle/config/routing/checkout.yml`` file.
**Remove routing** of ``sylius_shop_checkout_select_shipping``. The rest should remain the same.

.. code-block:: yaml

    # app/Resources/SyliusShopBundle/config/routing/checkout.yml
    sylius_shop_checkout_start:
        path: /
        methods: [GET]
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
                    type: Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType
                    options:
                        customer: expr:service('sylius.context.customer').getCustomer()
                repository:
                    method: find
                    arguments:
                        - "expr:service('sylius.context.cart').getCart()"
                state_machine:
                    graph: sylius_order_checkout
                    transition: address

    sylius_shop_checkout_select_payment:
        path: /select-payment
        methods: [GET, PUT]
        defaults:
            _controller: sylius.controller.order:updateAction
            _sylius:
                event: payment
                flash: false
                template: SyliusShopBundle:Checkout:selectPayment.html.twig
                form: Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType
                repository:
                    method: find
                    arguments:
                        - "expr:service('sylius.context.cart').getCart()"
                state_machine:
                    graph: sylius_order_checkout
                    transition: select_payment

    sylius_shop_checkout_complete:
        path: /complete
        methods: [GET, PUT]
        defaults:
            _controller: sylius.controller.order:completeAction
            _sylius:
                event: complete
                flash: false
                template: SyliusShopBundle:Checkout:complete.html.twig
                repository:
                    method: find
                    arguments:
                        - "expr:service('sylius.context.cart').getCart()"
                state_machine:
                    graph: sylius_order_checkout
                    transition: complete
                redirect:
                    route: sylius_shop_order_pay
                    parameters:
                        tokenValue: resource.tokenValue
                form:
                    type: Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType
                    options:
                        validation_groups: 'sylius_checkout_complete'

.. tip::

    If you do not see any changes run ``$ php bin/console cache:clear``.

Learn more
----------

* :doc:`Checkout - concept Documentation </book/orders/checkout>`
* :doc:`State Machine - concept Documentation </book/architecture/state_machine>`
* :doc:`Customization Guide </customization/index>`
