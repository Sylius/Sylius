Summary
=======

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_cart:
        # The driver used for persistence layer.
        driver: ~
        # Service id of cart item resolver.
        resolver: ~
        # Cart provider service id.
        provider: sylius.cart_provider.default
        # The id of cart storage for default provider.
        storage: sylius.cart_storage.session
        resources:
            cart:
                classes:
                    controller: Sylius\Bundle\CartBundle\Controller\CartController
                    form:       Sylius\Bundle\CartBundle\Form\Type\CartType
                validation_groups:
                    default: [ sylius ]
            cart_item:
                classes:
                    controller: Sylius\Bundle\CartBundle\Controller\CartItemController
                    form:       Sylius\Bundle\CartBundle\Form\Type\CartItemType
                validation_groups:
                    default: [ sylius ]

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -f pretty


Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
