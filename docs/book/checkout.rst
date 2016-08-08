.. index::
   single: Checkout

Checkout
========

**Checkout** is a process that begins when the Customer decides to finish their shopping and transform their **Cart** into an **Order**.

Checkout State Machine
----------------------

The Order Checkout state machine has 5 states available: ``cart``, ``addressed``, ``shipping_selected``, ``payment_selected``, ``completed``
and a set of defined transitions between them.

Besides the steps of checkout, each of them can be done once more. For instance if the Customer changes their mind
and after selecting payment he wants to change the shipping address he has already specified, he can of course go back and readdress it.

The transitions on the order checkout state machine are:

.. code-block:: yaml

   transitions:
      address:
          from: [cart]
          to: addressed
      readdress:
          from: [payment_selected, shipping_selected, addressed]
          to: cart
      select_shipping:
          from: [addressed]
          to: shipping_selected
      reselect_shipping:
          from: [payment_selected, shipping_selected]
          to: addressed
      select_payment:
          from: [shipping_selected]
          to: payment_selected
      reselect_payment:
          from: [payment_selected]
          to: shipping_selected
      complete:
          from: [payment_selected]
          to: completed

Steps of Checkout
-----------------

Checkout in Sylius is divided into 4 steps. Each of these steps occurs when the Order goes into a certain state.

.. note::

    Before performing Checkout :doc:`you need to have an Order created </book/orders>`.

Addressing
~~~~~~~~~~

This is a step where the customer provides both **shipping and billing addresses**.

+--------------------------+----------------------------------------------------+
| Transition after step    | Template                                           |
+--------------------------+----------------------------------------------------+
| ``cart``-> ``addressed`` | ``SyliusShopBundle:Checkout:addressing.html.twig`` |
+--------------------------+----------------------------------------------------+

How to perform the Addressing Step programmatically?
''''''''''''''''''''''''''''''''''''''''''''''''''''

Firstly if the **Customer** is not yet set on the Order it will be assigned depending on the case:

* An already logged in **User** - the Customer is set for the Order using the ``Sylius\Bundle\CoreBundle\EventListener\CartBlamerListener``, that determines the user basing on the event.
* An existent **User** that is not logged in - they are redirected to log in before continuing.
* A **Customer** that was present in the system before (we've got their e-mail) - the Customer instance is updated via cascade, the order is assigned to it.
* A new **Customer** with unknown e-mail - a new Customer instance is created and assigned to the order.

Of course the customer data like name and surname is also handled.

The typical **Address** consists of: country, city, street and postcode - to assign it to an Order either create it manually or retrieve from the repository.

.. code-block:: php

     /** @var AddressInterface $address */
     $address = $this->container->get('sylius.factory.address')->createNew();

     $address->setFirstName('Name');
     $address->setLastName('Surname');
     $address->setStreet('Street');
     $address->setCountryCode('PL');
     $address->setCity('City');
     $address->setPostcode('11111');

     $order->setShippingAddress($address);
     $order->setBillingAddress($address);

Having the **Customer** and the **Address** set you can apply a state transition to your order.
Get the StateMachine for the Order via the StateMachineFactory with a proper schema, and apply a transition.

.. code-block:: php

    $stateMachineFactory = $this->container->get('sm.factory');

    $stateMachine = $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)
    $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

**What happens with the transition?**

The method ``process($order)`` of the ``Sylius\Component\Core\OrderProcessing\OrderProcessor`` is run.
It is responsible for creating new **Shipments** for each OrderItemUnit of your order and a new **Payment** if they do not exist yet.
Therefore this transition is preparing the order for the two next steps of checkout.

Selecting shipping
~~~~~~~~~~~~~~~~~~

It is a step where the customer selects the way their order will be shipped to him.
Basing on the ShippingMethods configured in the system the options for the Customer are provided together with their prices.

+---------------------------------------+--------------------------------------------------+
| Transition after step                 | Template                                         |
+---------------------------------------+--------------------------------------------------+
| ``addressed``-> ``shipping_selected`` | ``SyliusShopBundle:Checkout:shipping.html.twig`` |
+---------------------------------------+--------------------------------------------------+

Selecting payment
~~~~~~~~~~~~~~~~~

This is a step where the customer chooses how are they willing to pay for their order.
Basing on the PaymentMethods configured in the system the possibilities for the Customer are provided.

+----------------------------------------------+-------------------------------------------------+
| Transition after step                        | Template                                        |
+----------------------------------------------+-------------------------------------------------+
| ``shipping_selected``-> ``payment_selected`` | ``SyliusShopBundle:Checkout:payment.html.twig`` |
+----------------------------------------------+-------------------------------------------------+

Finalizing
~~~~~~~~~~

In this step the customer gets an order summary and is redirected to complete the payment he has selected.

+--------------------------------------+-------------------------------------------------+
| Transition after step                | Template                                        |
+--------------------------------------+-------------------------------------------------+
| ``payment_selected``-> ``completed`` | ``SyliusShopBundle:Checkout:summary.html.twig`` |
+--------------------------------------+-------------------------------------------------+

Learn more
----------

* :doc:`State Machine - Documentation </book/state_machine>`
