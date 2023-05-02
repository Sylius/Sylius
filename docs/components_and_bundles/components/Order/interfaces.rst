.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_order_model_order-interface:

OrderInterface
~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Order.

.. hint::
    It also contains the default :doc:`/components_and_bundles/components/Order/state_machine`.

.. note::
    This interface extends `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_, `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_,
    :ref:`component_order_model_adjustable-interface` and :ref:`component_order_model_comment-aware-interface`

.. _component_order_model_order-aware-interface:

OrderAwareInterface
~~~~~~~~~~~~~~~~~~~

This interface provides basic operations for order management.
If you want to have orders in your model just implement this interface.

.. _component_order_model_order-item-interface:

OrderItemInterface
~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single OrderItem.

.. note::
    This interface extends the :ref:`component_order_model_order-aware-interface` and the :ref:`component_order_model_adjustable-interface`,

.. _component_order_model_order-item-unit-interface:

OrderItemUnitInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single OrderItemUnit.

.. note::
    This interface extends the :ref:`component_order_model_adjustable-interface`,

.. _component_order_model_adjustment-interface:

AdjustmentInterface
~~~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Adjustment.

.. note::
    This interface extends the `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_order_model_adjustable-interface:

AdjustableInterface
~~~~~~~~~~~~~~~~~~~

This interface provides basic operations for adjustment management.
Use this interface if you want to make a model adjustable.

For example following models implement this interface:
    * :ref:`component_order_model_order`
    * :ref:`component_order_model_order-item`

.. _component_order_model_comment-interface:

CommentInterface
~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Comment.

.. note::
    This interface extends the `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_

.. _component_order_model_comment-aware-interface:

CommentAwareInterface
~~~~~~~~~~~~~~~~~~~~~

This interface provides basic operations for comments management.
If you want to have comments in your model just implement this interface.

.. _component_order_model_identity-interface:

IdentityInterface
~~~~~~~~~~~~~~~~~

This interface should be implemented by model representing a single Identity. It can be used for storing external identifications.

Services Interfaces
-------------------

.. _component_order_repository_order-repository-interface:

OrderRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~

In order to decouple from storage that provides recently completed orders or check if given order's number is already used,
you should create repository class which implements this interface.

.. note::
    This interface extends the `RepositoryInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Repository/RepositoryInterface.php>`_.
