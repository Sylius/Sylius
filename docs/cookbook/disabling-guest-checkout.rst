How to disable guest checkout?
==============================

Sometimes, depending on your use case, you may want to resign from the guest checkout feature provided by Sylius.

In order to require users to have an account and be logged in before they can make an order in your shop,
you have to turn on the firewalls on the ``/checkout`` urls.

To achieve that simple add this path to ``access_control`` in the ``security.yml`` file.

.. code-block:: yaml

    # app/config/security.yml
    security:
        access_control:
            - { path: "%sylius.security.shop_regex%/checkout", role: ROLE_USER }

That will do the trick. Now, when a guest user tries to click the checkout button in the cart,
they will be redirected to the login/registration page, where after they sign in/sign up they
will be redirected to the checkout addressing step.

Learn more
----------

* :doc:`Sylius Checkout </book/orders/checkout>`
