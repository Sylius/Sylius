Shipping & Payment
==================

The basic configuration is done. We can now proceed to let potential customers buy our merchandise.
During the checkout process, they should be able to define how do they want their order to be shipped,
as well as how they would pay for that.

Shipping method
---------------

Sylius allows configuring different ways to ship the order, depending on shipping address (the **Zone** concept is essential there!),
or affiliation to some specific **Shipping Category**. Let's then create a shipping method called "FedEx" that would cost $10.00 for a whole order.

.. image:: /_images/getting-started-with-sylius/shipping-method.png

Payment method
--------------

Customer should also be able to choose, how they are willing to pay. At least one payment method is required - let's make it "Cash on delivery".
Before creation, we need to specify the payment method gateway, which is a way for processing the payment
(*Offline*, *PayPal Commerce Platform*, *PayPal Express Checkout* and *Stripe* are supported by default).

Gateway selection:

.. image:: /_images/getting-started-with-sylius/gateways.png
    :scale: 55%
    :align: center

|

Payment method creation:

.. image:: /_images/getting-started-with-sylius/payment-method-creation.png

.. attention::

    *Psst!* You can find integrations with more payment gateways if you take a look at some `Sylius plugins <https://sylius.com/plugins>`_

Great! The only thing left is creating some products, and we can go shopping!

Learn more
##########

* :doc:`Shipments </book/orders/shipments>`
* :doc:`Payments </book/orders/payments>`
