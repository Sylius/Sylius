Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_cart:
        driver: ~ # The driver used for persistence layer.
        engine: twig # Templating engine to use by default.
        resolver: ~ # Service id of cart item resolver.
        provider: sylius.cart_provider.default # Cart provider service id.
        storage: sylius.cart_storage.session # The id of cart storage for default provider.
        classes:
            cart:
                model: ~ # The cart model class.
                controller: Sylius\Bundle\CartBundle\Controller\CartController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\CartBundle\Form\Type\CartType # The form type name to use.
            item:
                model: ~ # The cart item model class.
                controller: Sylius\Bundle\CartBundle\Controller\CartItemController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\CartBundle\Form\Type\CartItemType # The form type class name to use.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -f pretty

Working examples
----------------

If you want to see working implementation, try out the `Sylius sandbox application <http://github.com/Sylius/Sylius-Sandbox>`_.

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/SyliusCartBundle/issues>`_.
If you have found bug, please create an issue.
