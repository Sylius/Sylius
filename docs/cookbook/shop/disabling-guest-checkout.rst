How to disable guest checkout?
==============================

Sometimes, depending on your use case, you may want to resign from the guest checkout feature provided by Sylius.

In order to require users to have an account and be logged in before they can make an order in your shop,
you have to turn on the firewalls on the ``/checkout`` urls.

To achieve that simple add this path to ``access_control`` in the ``security.yaml`` file.

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        access_control:
            - { path: "%sylius.security.shop_regex%/checkout", role: ROLE_USER }

That will do the trick. Now, when a guest user tries to click the checkout button in the cart,
they will be redirected to the login/registration page, where after they sign in/sign up they
will be redirected to the checkout addressing step.

How to disable guest checkout in new API?
=========================================

Our new API let's any anonymous user to proceed through the checkout out of the box (of course if API is enabled).
In order to disable checking out for anonymous user you have to change the same file but use different routes.
Here we will let user to only:

    * Pick up a new cart
    * Have access to it by its token value
    * Let the user add items to the cart

We will modify the same file as in example on top, the ``security.yaml``:

.. code-block:: yaml

    # config/packages/security.yaml
    security:
        access_control:
           - { path: "%sylius.security.new_api_shop_regex%/orders", method: POST, GET , role: IS_AUTHENTICATED_ANONYMOUSLY }
           - { path: "%sylius.security.new_api_shop_regex%/orders/.*/items", method: POST , role: IS_AUTHENTICATED_ANONYMOUSLY }
           - { path: "%sylius.security.new_api_shop_regex%/.*", role: ROLE_USER }

.. warning::

    The "main" path (in this example ``"%sylius.security.new_api_shop_regex%/.*"`` ) should be at the very end of the configuration
    with the same route, otherwise this would not work.

Now when an anonymous user will try to use other checkout routes they will be informed that they are not authenticated:

.. code-block:: bash

    # response
    {
      "code": 401,
      "message": "JWT Token not found"
    }

Learn more
----------

* :doc:`Sylius Checkout </book/orders/checkout>`
