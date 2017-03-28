.. index::
   single: State Machine

State Machine
=============

In **Sylius** we are using the `Winzou StateMachine Bundle <https://github.com/winzou/StateMachineBundle>`_.
State Machines are an approach to handling changes occurring in the system frequently, that is extremely flexible and very well organised.

Every state machine will have a predefined set of states, that will be stored on an entity that is being controlled by it.
These states will have a set of defined transitions between them, and a set of callbacks - a kind of events, that will happen on defined transitions.

States
------

States of a state machine are defined as constants on the model of an entity that the state machine is controlling.

How to configure states? Let's see on the example from **Checkout** state machine.

.. code-block:: yaml

   # CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
   winzou_state_machine:
       sylius_order_checkout:
           # list of all possible states:
           states:
               cart: ~
               addressed: ~
               shipping_selected: ~
               payment_selected: ~
               completed: ~

Transitions
-----------

On the graph it would be the connection between two states, defining that you can move from one state to another subsequently.

How to configure transitions? Let's see on the example of our **Checkout** state machine.
Having states configured we can have a transition between the ``cart`` state to the ``addressed`` state.

.. code-block:: yaml

   # CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
   winzou_state_machine:
       sylius_order_checkout:
           transitions:
               address:
                   from: [cart, addressed, shipping_selected, payment_selected]  # here you specify which state is the initial 
                   to: addressed                                                 # there you specify which state is final for that transition

Callbacks
---------

Callbacks are used to execute some code before or after applying transitions. Winzou StateMachineBundle adds the ability to use Symfony services in the callbacks.

How to configure callbacks?
Having a configured transition, you can attach a callback to it either before or after the transition. Callback is simply a method of a service you want to be executed.

.. code-block:: yaml

   # CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
   winzou_state_machine:
        sylius_order_checkout:
             callbacks:
                  # callbacks may be called before or after specified transitions, in the checkout state machine we've got callbacks only after transitions
                  after:
                       sylius_process_cart:
                           on: ["address", "select_shipping", "select_payment"]
                           do: ["@sylius.order_processing.order_processor", "process"]
                           args: ["object"]

Configuration
-------------

In order to use a state machine, you have to define a graph beforehand.
A graph is a definition of states, transitions and optionally callbacks - all attached on an object from your domain.
Multiple graphs may be attached to the same object.

In **Sylius** the best example of a state machine is the one from checkout. It has five states available:
``cart``, ``addressed``, ``shipping_selected``, ``payment_selected`` and ``completed`` - which can be achieved by applying some transitions to the entity.
For example, when selecting a shipping method during the shipping step of checkout we should apply the ``select_shipping`` transition, and after that the state
would become ``shipping_selected``.

.. code-block:: yaml

   # CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml
   winzou_state_machine:
       sylius_order_checkout:
           class: "%sylius.model.order.class%" # class of the domain object - in our case Order
           property_path: checkoutState
           graph: sylius_order_checkout
           state_machine_class: "%sylius.state_machine.class%"
           # list of all possible states:
           states:
               cart: ~
               addressed: ~
               shipping_selected: ~
               payment_selected: ~
               completed: ~
           # list of all possible transitions:
           transitions:
               address:
                   from: [cart, addressed, shipping_selected, payment_selected] # here you specify which state is the initial
                   to: addressed                                                # there you specify which state is final for that transition
               select_shipping:
                   from: [addressed, shipping_selected, payment_selected]
                   to: shipping_selected
               select_payment:
                   from: [payment_selected, shipping_selected]
                   to: payment_selected
               complete:
                   from: [payment_selected]
                   to: completed
           # list of all callbacks:
           callbacks:
           # callbacks may be called before or after specified transitions, in the checkout state machine we've got callbacks only after transitions
               after:
                   sylius_process_cart:
                       on: ["address", "select_shipping", "select_payment"]
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

Learn more
----------

* `Winzou StateMachine Bundle <https://github.com/winzou/StateMachineBundle>`_
* :doc:`Customization guide: State machines </customization/state_machine>`
