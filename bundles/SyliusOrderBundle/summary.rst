Summary
=======

.. note::

    To be written.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_order:
        driver: ~ # The driver used for persistence layer.
        classes:
            sellable:
                model: ~ # The class name of the entity you want to put inside orders.
            order:
                model: ~ # The order model class.
                controller: Sylius\Bundle\OrderBundle\Controller\OrderController
                repository: ~ # You can override the repository class here.
                form: Sylius\Bundle\OrderBundle\Form\Type\OrderType # The form type name to use.
            order_item:
                model: ~ # The order item model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\OrderBundle\Form\Type\OrderItemType # The form type class name to use.
            adjustment:
                model: ~ # The adjustment model class.
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\OrderBundle\Form\Type\AdjustmentType
        validation_groups:
            order: [sylius] # Order validation groups.
            order_item: [sylius] # Order item validation groups.

`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
