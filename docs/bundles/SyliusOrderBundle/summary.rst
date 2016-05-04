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
        resources:
            order:
                classes:
                    model:      Sylius\Component\Order\Model\Order
                    interface:  Sylius\Component\Order\Model\OrderInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\OrderBundle\Form\Type\OrderType
                validation_groups:
                     default: [ sylius ]
            order_item:
                classes:
                    model:      Sylius\Component\Order\Model\OrderItem
                    interface:  Sylius\Component\Order\Model\OrderItemInterface
                    controller: Sylius\Bundle\OrderBundle\Controller\OrderItemController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\OrderBundle\Form\Type\OrderItemType
                validation_groups:
                     default: [ sylius ]
            order_item_unit:
                classes:
                    model:      Sylius\Component\Order\Model\OrderItemUnit
                    interface:  Sylius\Component\Order\Model\OrderItemUnit\Interface
                    repository: ~
                    factory:    Sylius\Bundle\OrderBundle\Factory\OrderItemUnitFactory
            order_identity:
                classes:
                    model:     Sylius\Component\Order\Model\Identity
                    interface: Sylius\Component\Order\Model\IdentityInterface
                    factory:   Sylius\Component\Resource\Factory\Factory
            adjustment:
                classes
                    model:      Sylius\Component\Order\Model\Adjustment
                    interface:  Sylius\Component\Order\Model\AdjustmentInterface
                    controller: Sylius\Bundle\OrderBundle\Controller\AdjustmentController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\OrderBundle\Form\Type\AdjustmentType
                validation_groups:
                     default: [ sylius ]
            comment:
                classes:
                    model:      Sylius\Component\Order\Model\Comment
                    interface:  Sylius\Component\Order\Model\CommentInterface
                    controller: Sylius\Bundle\OrderBundle\Controller\CommentController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\OrderBundle\Form\Type\CommentType
                validation_groups:
                     default: [ sylius ]


`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
