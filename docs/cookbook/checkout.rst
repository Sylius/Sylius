How to customize Sylius Checkout?
=================================

Why would you override the Checkout process?
--------------------------------------------

How to remove a step from checkout?
-----------------------------------

Let's imagine that you are trying to create a shop that does not need shipping - it sells downloadable files only.

To meet your needs you will need to adjust checkout process. **What do you have to do then?** See below:

Overwrite the state machine of Checkout
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open the ``CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml`` and place its content in
the ``app/config/state_machine.yml`` which has to be imported in the ``app/config/config.yml``.
Remove the ``shipment_selected`` state, ``select_shipment`` transition. Remove the ``select_shipment`` from the
``sylius_process_cart`` callback.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "state_machine.yml" }

.. code-block:: yaml

    # app/config/state_machine.yml
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


Learn more
----------

* :doc:`Checkout - concept Documentation </book/checkout>`
* :doc:`State Machine - concept Documentation </book/state_machine>`
* :doc:`Customization Guide </customization/index>`
