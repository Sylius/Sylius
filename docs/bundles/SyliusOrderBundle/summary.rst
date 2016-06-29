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
                    model:      Sylius\Order\Model\Order
                    interface:  Sylius\Order\Model\OrderInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\OrderBundle\Form\Type\OrderType
                validation_groups:
                     default: [ sylius ]
            order_item:
                classes:
                    model:      Sylius\Order\Model\OrderItem
                    interface:  Sylius\Order\Model\OrderItemInterface
                    controller: Sylius\OrderBundle\Controller\OrderItemController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\OrderBundle\Form\Type\OrderItemType
                validation_groups:
                     default: [ sylius ]
            order_item_unit:
                classes:
                    model:      Sylius\Order\Model\OrderItemUnit
                    interface:  Sylius\Order\Model\OrderItemUnit\Interface
                    repository: ~
                    factory:    Sylius\OrderBundle\Factory\OrderItemUnitFactory
            order_identity:
                classes:
                    model:     Sylius\Order\Model\Identity
                    interface: Sylius\Order\Model\IdentityInterface
                    factory:   Sylius\Resource\Factory\Factory
            adjustment:
                classes
                    model:      Sylius\Order\Model\Adjustment
                    interface:  Sylius\Order\Model\AdjustmentInterface
                    controller: Sylius\OrderBundle\Controller\AdjustmentController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\OrderBundle\Form\Type\AdjustmentType
                validation_groups:
                     default: [ sylius ]
            comment:
                classes:
                    model:      Sylius\Order\Model\Comment
                    interface:  Sylius\Order\Model\CommentInterface
                    controller: Sylius\OrderBundle\Controller\CommentController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\OrderBundle\Form\Type\CommentType
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
