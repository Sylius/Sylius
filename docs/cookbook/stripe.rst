How to configure Stripe Credit Card payment?
============================================

One of very important payment methods in e-commerce are credit cards. Payments via credit card are in Sylius supported by `Stripe <http://stripe.com/docs>`_.

Install Stripe
--------------

Stripe is not available by default in Sylius, to have it you need to add its package via composer.

.. code-block:: bash

    $ php composer require stripe/stripe-php:~2.0

Configure the gateway
---------------------

.. note::

    To test this configuration properly you will need a `developer account on Stripe <https://dashboard.stripe.com/register>`_.
    Use its data (``secret_key`` and ``publishable_key``) in the **parameters.yml** file.

.. code-block:: yaml

    # app/config/parameters.yml
    parameters:
        stripe.secret_key: TEST
        stripe.publishable_key: TEST

Having these parameters defined you can configure the gateway inside the ``app/config/payum.yml`` file which has to be imported in the ``app/config/config.yml``.

.. code-block:: yaml

    # app/config/payum.yml
    payum:
        gateways:
            credit_card:
                factory: stripe_checkout
                secret_key: "%stripe.secret_key%"
                publishable_key: "%stripe.publishable_key%"
                payum.template.layout: SyliusShopBundle::Checkout/layout.html.twig
                payum.template.obtain_token: SyliusPayumBundle::Action/Stripe/obtainCheckoutToken.html.twig

.. code-block:: yaml

    # app/config/config.yml
    imports:
        - { resource: "payum.yml" }

Add a payment method with the Stripe Credit card gateway in the Admin Panel
---------------------------------------------------------------------------

* Create a new payment method and choose the ``Stripe Credit Card`` gateway for it.

* Add the new method to a channel.

.. tip::

    If your are not sure how to do it check how we do it :doc:`for Paypal in this cookbook </cookbook/paypal>`.

Choosing Stripe Credit Card method in Checkout
----------------------------------------------

From now on Stripe Credit Card will be available in Checkout in the channel you have added it to.

**Done!**

Learn more
----------

* :doc:`Payments concept documentation </book/payments>`
* `Payum - Project Documentation <https://github.com/Payum/Payum/blob/master/src/Payum/Core/Resources/docs/index.md>`_
