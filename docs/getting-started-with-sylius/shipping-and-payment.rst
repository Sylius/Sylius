Shipping & Payment
==================

The basic configuration is done. We can now proceed to allow potential customers buying our merchandise. As usual, during the checkout
process, they should be able to define how do they want their order to be shipped, as well as how they would pay for that.

Shipping method
---------------

Sylius allows configuring different ways to ship the order, depending on shipping address (the **Zone** concept is essential there!), or affiliation
to some specific **Shipping Category**. Let's then create a simple shipping method called "FedEx" that would cost $10.00 for the whole order.

.. image:: /_images/getting-started-with-sylius/shipping-method.png

Payment method
--------------

Customer should also be able to choose, how their order will be paid. At least one payment method is required for completing a checkout, so create a new one
named "Cash on delivery". Before creation, we need to specify the payment method gateway, which is a way for processing the payment (*Offline*, *PayPal Express Checkout*,
and *Stripe* are supported by default).

Gateway selection:

.. image:: /_images/getting-started-with-sylius/gateways.png
    :scale: 55%
    :align: center

|

Payment method creation:

.. image:: /_images/getting-started-with-sylius/payment-method-creation.png

.. attention::

    Psst! You can find integrations with more payment gateways if you take a look at some `Sylius plugins <https://sylius.com/plugins>`_

Great! The only thing left is creating some product, and we can go shopping!

Learn more
##########

* :doc:`Shipments </book/orders/shipments>`
* :doc:`Payments </book/orders/payments>`
