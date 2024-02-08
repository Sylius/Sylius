.. warning::

    On September 14, 2019 the Strong Customer Authentication (SCA) requirement has been introduced.
    The implementation provided by Sylius Core was not *SCA Ready* and has been deprecated.
    Please have a look at the `official documentation of Stripe regarding this topic <https://stripe.com/guides/strong-customer-authentication>`_.

.. rst-class:: outdated

How to configure Stripe Credit Card payment?
============================================

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

One of very important payment methods in e-commerce are credit cards. Payments via credit card are in Sylius supported by `Stripe <https://stripe.com/docs>`_.

Install Stripe
--------------

Stripe is not available by default in Sylius, to have it you need to add its package via composer.

.. code-block:: bash

    php composer require stripe/stripe-php:~4.1

Add a payment method with the Stripe gateway in the Admin Panel
---------------------------------------------------------------

.. note::

    To test this configuration properly you will need a `developer account on Stripe <https://dashboard.stripe.com/register>`_.

* Create a new payment method, choosing the ``Stripe Credit Card`` gateway from the gateways choice dropdown and enable it for chosen channels.

Go to the ``https://localhost:8000/admin/payment-methods/new/stripe_checkout`` url.

* Fill in the Stripe configuration form with your developer account data (``publishable_key`` and ``secret_key``).
* Save the new payment method.

.. tip::

    If your are not sure how to do it check how we do it :doc:`for Paypal in this cookbook </cookbook/payments/paypal>`.

.. warning::

    When your project is behind a loadbalancer and uses https you probably need to configure `trusted proxies <https://symfony.com/doc/current/deployment/proxies.html>`_. Otherwise the payment will not succeed and the user will endlessly loopback to the payment page without any notice.

Choosing Stripe Credit Card method in Checkout
----------------------------------------------

From now on Stripe Credit Card will be available in Checkout in the channel you have added it to.

**Done!**

Learn more
----------

* :doc:`Payments concept documentation </book/orders/payments>`
* `Payum - Project Documentation <https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md>`_
