How to configure Stripe Credit Card payment?
============================================

One of very important payment methods in e-commerce are credit cards. Payments via credit card are in Sylius supported by `Stripe <http://stripe.com/docs>`_.

Install Stripe
--------------

Stripe is not available by default in Sylius, to have it you need to add its package via composer.

.. code-block:: bash

    $ php composer require stripe/stripe-php:~2.0

Add a payment method with the Stripe gateway in the Admin Panel
---------------------------------------------------------------

.. note::

    To test this configuration properly you will need a `developer account on Stripe <https://dashboard.stripe.com/register>`_.

* Create a new payment method, choosing the ``Stripe Credit Card`` gateway from the gateways choice dropdown and enable it for chosen channels.

Go to the ``http://localhost:8000/admin/payment-methods/new/stripe_checkout`` url.

* Fill in the Stripe configuration form with your developer account data (``secret_key``, ``publishable_key``, ``layout_template`` and ``obtain_token_template``).
* Save the new payment method.

.. tip::

    If your are not sure how to do it check how we do it :doc:`for Paypal in this cookbook </cookbook/paypal>`.

Choosing Stripe Credit Card method in Checkout
----------------------------------------------

From now on Stripe Credit Card will be available in Checkout in the channel you have added it to.

**Done!**

Learn more
----------

* :doc:`Payments concept documentation </book/orders/payments>`
* `Payum - Project Documentation <https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md>`_
