Customizing State Machines
==========================

.. warning::

    Not familiar with the State Machine concept? Read the docs :doc:`here </book/architecture/state_machine>`!

.. note::

    **Customizing logic via State Machines vs. Events**

    The logic in which Sylius operates can be customized in two ways. First of them is using the state machines: what is
    really useful when you need to modify business logic for instance modify the flow of the checkout,
    and the second is listening on the kernel events related to the entities, which is helpful for modifying the HTTP responses
    visible directly to the user, like displaying notifications, sending emails.

How to customize a State Machine?
---------------------------------

.. tip::

    First of all if you are attempting to change anything in any state machine in **Sylius** you will need a special file:
    ``app/config/state_machine.yml`` which has to be imported in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "state_machine.yml" }

How to add a new state?
~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add a new **state** to :doc:`the Order state machine </book/orders/orders>`.
You will need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order:
            states:
                your_new_state: ~ # here name your state as you wish

After that your new step will be available alongside other steps that already were defined in that state machine.

.. tip::

    Run ``$ php bin/console debug:winzou:state-machine sylius_order``
    to check if the state machine has changed to your implementation.

How to add a new transition?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add a new **transition** to :doc:`the Order state machine </book/orders/orders>`,
that will allow moving from the ``cancelled`` state backwards to ``new``. Let's call it "restoring".

You will need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order:
            transitions:
                restore:
                    from: [cancelled]
                    to: new

After that your new transition will be available alongside other transitions that already were defined in that state machine.

.. tip::

    Run ``$ php bin/console debug:winzou:state-machine sylius_order``
    to check if the state machine has changed to your implementation.

How to remove a state and its transitions?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. warning::

    If you are willing to remove a state or a transition you have to override **the whole states/transitions section**
    of the state machine you are willing to modify. See how we do it in the :doc:`customization of the Checkout process </cookbook/checkout>`.

How to add a new callback?
~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add a new **callback** to :doc:`the Order state machine </book/orders/orders>`,
that will do something on an already defined transition.

You will need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order:
            callbacks:
                after:
                    sylius_send_email:
                        # here you are choosing the transition on which the action should take place - we are using the one we have created before
                        on: ["cancel"]
                        # it is just an example, use an existent service and its method here!
                        do: ["@service", "sendEmail"]
                        # this will be the object of an Order here
                        args: ["object"]

After that your new callback will be available alongside other callbacks that already were defined in that state machine
and will be called on the desired transition

How to modify a callback?
~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to modify an existent callback of for example the state machine of ProductReviews,
so that it does not count the average rating but does something else - you need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_review:
            callbacks:
                after:
                    update_price:
                        on: "accept"
                        # here you can change the service and its method that is called for your own service
                        do: ["@sylius.review.updater.your_service", update]
                        args: ["object"]

How to disable a callback?
~~~~~~~~~~~~~~~~~~~~~~~~~~

If you would like to turn off a callback of a state machine you need to set its ``disabled`` option to true.
On the example of the state machine of ProductReview, we can turn off the ``update_price`` callback:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_review:
            callbacks:
                after:
                    update_price:
                        disabled: true

Learn more
----------

* `Winzou StateMachine Bundle <https://github.com/winzou/StateMachineBundle>`_
* :doc:`State Machine Concept </book/architecture/state_machine>`
