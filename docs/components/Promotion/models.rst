Models
======

.. _component_promotion_model_promotion:

Promotion
---------

The promotion is represented by a **Promotion** instance. It has the following properties as default:

+----------------+-------------------------------------------------------------+
| Property       | Description                                                 |
+================+=============================================================+
| id             | Unique id of the promotion                                  |
+----------------+-------------------------------------------------------------+
| code           | Unique code of the promotion                                |
+----------------+-------------------------------------------------------------+
| name           | Promotion's name                                            |
+----------------+-------------------------------------------------------------+
| description    | Promotion's description                                     |
+----------------+-------------------------------------------------------------+
| priority       | When exclusive, promotion with top priority will be applied |
+----------------+-------------------------------------------------------------+
| exclusive      | Cannot be applied together with other promotions            |
+----------------+-------------------------------------------------------------+
| usageLimit     | Promotion's usage limit                                     |
+----------------+-------------------------------------------------------------+
| used           | Number of times this coupon has been used                   |
+----------------+-------------------------------------------------------------+
| startsAt       | Start date                                                  |
+----------------+-------------------------------------------------------------+
| endsAt         | End date                                                    |
+----------------+-------------------------------------------------------------+
| couponBased    | Whether this promotion is triggered by a coupon             |
+----------------+-------------------------------------------------------------+
| coupons        | Associated coupons                                          |
+----------------+-------------------------------------------------------------+
| rules          | Associated rules                                            |
+----------------+-------------------------------------------------------------+
| actions        | Associated actions                                          |
+----------------+-------------------------------------------------------------+
| createdAt      | Date of creation                                            |
+----------------+-------------------------------------------------------------+
| updatedAt      | Date of update                                              |
+----------------+-------------------------------------------------------------+

.. note::

    This model implements the :ref:`component_promotion_model_promotion-interface` .


.. _component_promotion_model_coupon:

Coupon
------

The coupon is represented by a **Coupon** instance. It has the following properties as default:

+----------------+---------------------------------------------------+
| Property       | Description                                       |
+================+===================================================+
| id             | Unique id of the coupon                           |
+----------------+---------------------------------------------------+
| code           | Coupon's code                                     |
+----------------+---------------------------------------------------+
| usageLimit     | Coupon's usage limit                              |
+----------------+---------------------------------------------------+
| used           | Number of times the coupon has been used          |
+----------------+---------------------------------------------------+
| promotion      | Associated promotion                              |
+----------------+---------------------------------------------------+
| expiresAt      | Expiration date                                   |
+----------------+---------------------------------------------------+
| createdAt      | Date of creation                                  |
+----------------+---------------------------------------------------+
| updatedAt      | Date of update                                    |
+----------------+---------------------------------------------------+

.. note::

    This model implements the :ref:`component_promotion_model_coupon-interface`.

.. _component_promotion_model_rule:

PromotionRule
-------------

The promotion rule is represented by a **PromotionRule** instance. PromotionRule is a requirement that has to be satisfied by the promotion subject.
It has the following properties as default:

+----------------+------------------------------------------+
| Property       | Description                              |
+================+==========================================+
| id             | Unique id of the coupon                  |
+----------------+------------------------------------------+
| type           | Rule's type                              |
+----------------+------------------------------------------+
| configuration  | Rule's configuration                     |
+----------------+------------------------------------------+
| promotion      | Associated promotion                     |
+----------------+------------------------------------------+

.. note::

    This model implements the :ref:`component_promotion_model_rule-interface`.


.. _component_promotion_model_action:

PromotionAction
---------------

The promotion action is represented by an **PromotionAction** instance. PromotionAction takes place if the rules of a promotion are satisfied.
It has the following properties as default:

+----------------+------------------------------------------+
| Property       | Description                              |
+================+==========================================+
| id             | Unique id of the action                  |
+----------------+------------------------------------------+
| type           | Rule's type                              |
+----------------+------------------------------------------+
| configuration  | Rule's configuration                     |
+----------------+------------------------------------------+
| promotion      | Associated promotion                     |
+----------------+------------------------------------------+

.. note::

    This model implements the :ref:`component_promotion_model_action-interface`.
