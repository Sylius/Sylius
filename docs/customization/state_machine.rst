Customizing State Machines
==========================

.. warning::
    Not familiar with the State Machine concept? Read the docs :doc:`here </book/state_machine>`!

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

Let's assume that you would like to add a new **state** to the state machine of :doc:`Order </book/orders>`.
You will need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order:
            states:
                your_new_state: ~ # here name your state as you wish

After that your new step will be available alongside other steps that already were definded in that state machine.

How to add a new transition?
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add a new **transition** to the state machine of :doc:`Order </book/orders>`,
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

After that your new transition will be available alongside other transitions that already were definded in that state machine.

How to add a new callback?
~~~~~~~~~~~~~~~~~~~~~~~~~~

Let's assume that you would like to add a new **callback** to the state machine of :doc:`Order </book/orders>`,
that will do something on an already defined transition.

You will need to add these few lines to the ``state_machine.yml``:

.. code-block:: yaml

    # app/config/state_machine.yml
    winzou_state_machine:
        sylius_order:e
            callbacks:
                after:
                    sylius_send_email:
                        # here you are choosing the transition on which the action should take place - we are using the one we have created abefore
                        on: ["cancel"]
                        # it is just an example, use an existent service and its method here!
                        do: ["@service", "sendEmail"]
                        # this will be the object of an Order here
                        args: ["object"]

After that your new callback will be available alongside other callbacks that already were definded in that state machine
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
                        # Here you can change the service and its method that is called for your own service
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
* :doc:`State Machine Concept </book/state_machine>`