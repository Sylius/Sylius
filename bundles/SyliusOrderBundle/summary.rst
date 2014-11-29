Summary
=======

.. note::

    To be written.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_order:
        # The driver used for persistence layer.
        driver: ~
        classes:
            sellable:
                # The class name of the entity you want to put inside orders.
                model: ~
            order:
                model: Sylius\Component\Order\Model\Order
                controller: Sylius\Bundle\OrderBundle\Controller\OrderController
                repository: ~
                form: Sylius\Bundle\OrderBundle\Form\Type\OrderType
            order_item:
                model: Sylius\Component\Order\Model\OrderItem
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\OrderBundle\Form\Type\OrderItemType
            adjustment:
                model: Sylius\Component\Order\Model\Adjustment
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\OrderBundle\Form\Type\AdjustmentType
        validation_groups:
            order: [sylius] # Order validation groups.
            order_item: [sylius]
            adjustment: [sylius]

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
