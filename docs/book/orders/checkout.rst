.. index::
   single: Checkout

Checkout
========

**Checkout** is a process that begins when the Customer decides to finish their shopping and pay for their order.
The process of specifying address, payment and a way of shipping transforms the **Cart** into an **Order**.

Checkout State Machine
----------------------

The Order Checkout state machine has 7 states available: ``cart``, ``addressed``, ``shipping_selected``,  ``shipping_skipped``, ``payment_selected``, ``payment_skipped``, ``completed`` and a set of defined transitions between them.
These states are saved as the **checkoutState** of the **Order**.

Besides the steps of checkout, each of them can be done more than once. For instance if the Customer changes their mind
and after selecting payment they want to change the shipping address they have already specified, they can of course go back and readdress it.

The transitions on the order checkout state machine are:

.. code-block:: yaml

    transitions:
        address:
            from: [cart, addressed, shipping_selected, shipping_skipped, payment_selected, payment_skipped]
            to: addressed
        skip_shipping:
            from: [addressed]
            to: shipping_skipped
        select_shipping:
            from: [addressed, shipping_selected, payment_selected, payment_skipped]
            to: shipping_selected
        skip_payment:
            from: [shipping_selected, shipping_skipped]
            to: payment_skipped
        select_payment:
            from: [payment_selected, shipping_skipped, shipping_selected]
            to: payment_selected
        complete:
            from: [payment_selected, payment_skipped]
            to: completed

.. image:: ../../_images/sylius_order_checkout.png
    :align: center
    :scale: 70%

Steps of Checkout
-----------------

Checkout in Sylius is divided into 4 steps. Each of these steps occurs when the Order goes into a certain state.
See the Checkout state machine in the `state_machine/sylius_order_checkout.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/CoreBundle/Resources/config/app/state_machine/sylius_order_checkout.yml>`_
together with the routing file for checkout: `checkout.yml <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/ShopBundle/Resources/config/routing/checkout.yml>`_.

.. note::

    Before performing Checkout :doc:`you need to have an Order created </book/orders/orders>`.

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

* An already logged in **User** - the Customer is set for the Order using the `ShopCartBlamerListener <https://github.com/Sylius/Sylius/blob/1.12/src/Sylius/Bundle/ShopBundle/EventListener/ShopCartBlamerListener.php>`_, that determines the user basing on the event.
* A **Customer** or **User** that was present in the system before (we've got their e-mail) - the Customer instance is updated via cascade, the order is assigned to it.
* A new **Customer** with unknown e-mail - a new Customer instance is created and assigned to the order.

.. note::

    Before Sylius ``v1.7`` a **User** (i.e. we have their e-mail and they are registered) had to login before they could complete the checkout process. If you want this constraint on your checkout, you can add this to your application:

    .. code-block:: xml

         <?xml version="1.0" encoding="UTF-8"?>

        <constraint-mapping xmlns="http://symfony.com/schema/dic/constraint-mapping" xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://symfony.com/schema/dic/constraint-mapping https://symfony.com/schema/dic/services/constraint-mapping-1.0.xsd">
            <class name="Sylius\Component\Core\Model\Customer">
                <constraint name="Sylius\Bundle\CoreBundle\Validator\Constraints\RegisteredUser">
                    <option name="message">sylius.customer.email.registered</option>
                    <option name="groups">sylius_customer_checkout_guest</option>
                </constraint>
            </class>
        </constraint-mapping>

    If you would like to achieve the same behaviour in API, read :doc:`the dedicated cookbook </cookbook/api/how_force_login_already_registered_user_during_checkout>`.

.. hint::

    If you do not understand the Users and Customers concept in Sylius go to the :doc:`Users Concept documentation </book/customers/customer_and_shopuser>`.

The typical **Address** consists of: country, city, street and postcode - to assign it to an Order either create it manually or retrieve from the repository.

.. code-block:: php

     /** @var AddressInterface $address */
     $address = $this->container->get('sylius.factory.address')->createNew();

     $address->setFirstName('Anne');
     $address->setLastName('Shirley');
     $address->setStreet('Avonlea');
     $address->setCountryCode('CA');
     $address->setCity('Canada');
     $address->setPostcode('C0A 1N0');

     $order->setShippingAddress($address);
     $order->setBillingAddress($address);

Having the **Customer** and the **Address** set you can apply a state transition to your order.
Get the StateMachine for the Order via the StateMachineFactory with a proper schema, and apply a transition
and of course flush your order after that via the manager.

.. code-block:: php

    $stateMachineFactory = $this->container->get('sm.factory');

    $stateMachine = $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
    $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);

    $this->container->get('sylius.manager.order')->flush();

**What happens during the transition?**

The method ``process($order)`` of the `CompositeOrderProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Order/Processor/CompositeOrderProcessor.php>`_ is run.

Selecting shipping
~~~~~~~~~~~~~~~~~~

It is a step where the customer selects the way their order will be shipped to them.
Basing on the ShippingMethods configured in the system the options for the Customer are provided together with their prices.

+---------------------------------------+--------------------------------------------------+
| Transition after step                 | Template                                         |
+---------------------------------------+--------------------------------------------------+
| ``addressed``-> ``shipping_selected`` | ``SyliusShopBundle:Checkout:shipping.html.twig`` |
+---------------------------------------+--------------------------------------------------+

How to perform the Selecting shipping Step programmatically?
''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Before approaching this step be sure that your Order is in the ``addressed`` state. In this state your order
will already have a default ShippingMethod assigned, but in this step you can change it and have everything recalculated automatically.

Firstly either create new (see how in the `Shipments concept </book/orders/shipments>`_) or retrieve a **ShippingMethod**
from the repository to assign it to your order's shipment created defaultly in the addressing step.

.. code-block:: php

    // Let's assume you have a method with code 'DHL' that has everything set properly
    $shippingMethod = $this->container->get('sylius.repository.shipping_method')->findOneByCode('DHL');

    // Shipments are a Collection, so even though you have one Shipment by default you have to iterate over them
    foreach ($order->getShipments() as $shipment) {
        $shipment->setMethod($shippingMethod);
    }

After that get the StateMachine for the Order via the StateMachineFactory with a proper schema,
and apply a proper transition and flush the order via the manager.

.. code-block:: php

    $stateMachineFactory = $this->container->get('sm.factory');

    $stateMachine = $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
    $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);

    $this->container->get('sylius.manager.order')->flush();

**What happens during the transition?**

The method ``process($order)`` of the `CompositeOrderProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Order/Processor/CompositeOrderProcessor.php>`_ is run.
Here this method is responsible for: controlling the **shipping charges** which depend on the chosen ShippingMethod,
controlling the **promotions** that depend on the shipping method.

Skipping shipping step
''''''''''''''''''''''

What if in the order you have only products that do not require shipping (they are downloadable for example)?

.. note::

    When all of the :doc:`ProductVariants </book/products/products>` of the order have the ``shippingRequired``
    property set to ``false``, then Sylius assumes that the whole order **does not require shipping**,
    and **the shipping step of checkout will be skipped**.

Selecting payment
~~~~~~~~~~~~~~~~~

This is a step where the customer chooses how are they willing to pay for their order.
Basing on the PaymentMethods configured in the system the possibilities for the Customer are provided.

+----------------------------------------------+-------------------------------------------------+
| Transition after step                        | Template                                        |
+----------------------------------------------+-------------------------------------------------+
| ``shipping_selected``-> ``payment_selected`` | ``SyliusShopBundle:Checkout:payment.html.twig`` |
+----------------------------------------------+-------------------------------------------------+

How to perform the Selecting payment step programmatically?
'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''

Before this step your Order should be in the ``shipping_selected`` state. It will have a default Payment selected after the addressing step,
but in this step you can change it.

Firstly either create new (see how in the `Payments concept </book/orders/payments>`_) or retrieve a **PaymentMethod**
from the repository to assign it to your order's payment created defaultly in the addressing step.

.. code-block:: php

    // Let's assume that you have a method with code 'paypal' configured
    $paymentMethod = $this->container->get('sylius.repository.payment_method')->findOneByCode('paypal');

    // Payments are a Collection, so even though you have one Payment by default you have to iterate over them
    foreach ($order->getPayments() as $payment) {
        $payment->setMethod($paymentMethod);
    }

After that get the StateMachine for the Order via the StateMachineFactory with a proper schema,
and apply a proper transition and flush the order via the manager.

.. code-block:: php

    $stateMachineFactory = $this->container->get('sm.factory');

    $stateMachine = $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
    $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);

    $this->container->get('sylius.manager.order')->flush();

**What happens during the transition?**

The method ``process($order)`` of the
`CompositeOrderProcessor <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Component/Order/Processor/CompositeOrderProcessor.php>`_
is run and checks all the adjustments on the order.

Finalizing
~~~~~~~~~~

In this step the customer gets an order summary and is redirected to complete the payment they have selected.

+--------------------------------------+-------------------------------------------------+
| Transition after step                | Template                                        |
+--------------------------------------+-------------------------------------------------+
| ``payment_selected``-> ``completed`` | ``SyliusShopBundle:Checkout:summary.html.twig`` |
+--------------------------------------+-------------------------------------------------+

.. note::

    The order will be processed through ``OrderIntegrityChecker`` in case to validate promotions applied to the order. If any of the promotions will expire during the finalizing checkout processor will remove this promotion and recalculate the order and update it.

How to complete Checkout programmatically?
''''''''''''''''''''''''''''''''''''''''''

Before executing the completing transition you can set some notes to your order.

.. code-block:: php

    $order->setNotes('Thank you dear shop owners! I am allergic to tape so please use something else for packaging.');

After that get the StateMachine for the Order via the StateMachineFactory with a proper schema,
and apply a proper transition and flush the order via the manager.

.. code-block:: php

    $stateMachineFactory = $this->container->get('sm.factory');

    $stateMachine = $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
    $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);

    $this->container->get('sylius.manager.order')->flush();

**What happens during the transition?**

* The Order will have the **checkoutState** - ``completed``,
* The Order will have the general **state** - ``new`` instead of ``cart`` it has had before the transition,
* When the Order is transitioned from ``cart`` to ``new`` the **paymentState** is set to ``awaiting_payment`` and the **shippingState** to ``ready``

The Checkout is finished after that.

Checkout related events
-----------------------

On each step of checkout a dedicated event is triggered.

+-----------------------------------------+
| Event id                                |
+=========================================+
| ``sylius.order.pre_address``            |
+-----------------------------------------+
| ``sylius.order.post_address``           |
+-----------------------------------------+
| ``sylius.order.pre_select_shipping``    |
+-----------------------------------------+
| ``sylius.order.post_select_shipping``   |
+-----------------------------------------+
| ``sylius.order.pre_payment``            |
+-----------------------------------------+
| ``sylius.order.post_payment``           |
+-----------------------------------------+
| ``sylius.order.pre_complete``           |
+-----------------------------------------+
| ``sylius.order.post_complete``          |
+-----------------------------------------+

Learn more
----------

* :doc:`State Machine - Documentation </book/architecture/state_machine>`
* :doc:`Orders - Concept Documentation </book/orders/orders>`
