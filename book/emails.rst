.. index::
   single: E-Mails

E-Mails
=======

Sylius is sending various e-mails and this chapter is a reference about all of them. Continue reading to learn what e-mails are sent, when and how to customize the templates.

Customer Welcome E-Mail
-----------------------

Every time new customer registers via registration form or checkout, this e-mail is sent to him.

.. code-block:: text

    SyliusWebBundle:Frontend/Email:customerWelcome.html.twig

You also have the following parameters available:

user
    Instance of the user entity

Order Confirmation
------------------

This e-mail is sent when order is paid. Template name is:

.. code-block:: text

    SyliusWebBundle:Frontend/Email:orderConfirmation.html.twig

You also have the following parameters available:

order
    Instance of the user order
order.user
    Customer
order.shippingAddress
    Shipping address
order.billingAddress
    Billing address
order.items
    Collection of order items

Order Comment
-------------

In the backend, you can comment orders and optionally notify the customer, this template is used:

.. code-block:: text

    SyliusWebBundle:Frontend/Email:orderComment.html.twig

You also have the following parameters available:

comment:
    Comment instance
order
    Instance of the user order
order.user
    Customer
order.shippingAddress
    Shipping address
order.billingAddress
    Billing address
order.items
    Collection of order items

Final Thoughts
--------------

...

Learn more
----------

* ...
