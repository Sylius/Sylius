Summary
=======

.. note::

    To be written.

Configuration reference
-----------------------

.. code-block:: yaml

    sylius_order:
        driver: doctrine/orm
        resources:
            order:
                classes:
                    model: Sylius\Component\Core\Model\Order
                    controller: Sylius\Bundle\CoreBundle\Controller\OrderController
                    repository: Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository
                    form: Sylius\Bundle\CoreBundle\Form\Type\Order\OrderType
                    interface: Sylius\Component\Order\Model\OrderInterface
                    factory: Sylius\Component\Resource\Factory\Factory
            order_item:
                classes:
                    model: Sylius\Component\Core\Model\OrderItem
                    form: Sylius\Bundle\CoreBundle\Form\Type\Order\OrderItemType
                    interface: Sylius\Component\Order\Model\OrderItemInterface
                    controller: Sylius\Bundle\OrderBundle\Controller\OrderItemController
                    factory: Sylius\Component\Resource\Factory\Factory
            order_item_unit:
                classes:
                    model: Sylius\Component\Core\Model\OrderItemUnit
                    interface: Sylius\Component\Order\Model\OrderItemUnitInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Order\Factory\OrderItemUnitFactory
            adjustment:
                classes:
                    model: Sylius\Component\Order\Model\Adjustment
                    interface: Sylius\Component\Order\Model\AdjustmentInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    factory: Sylius\Component\Resource\Factory\Factory
            order_sequence:
                classes:
                    model: Sylius\Component\Order\Model\OrderSequence
                    interface: Sylius\Component\Order\Model\OrderSequenceInterface
                    factory: Sylius\Component\Resource\Factory\Factory
        expiration:
            cart: '2 days'
            order: '5 days'


`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
