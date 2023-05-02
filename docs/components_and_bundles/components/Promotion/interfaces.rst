.. rst-class:: outdated

Interfaces
==========

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

Model Interfaces
----------------

.. _component_promotion_model_promotion-subject-interface:

PromotionSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~

To characterize an object with attributes and options from a promotion, the object class needs to implement
the **PromotionSubjectInterface**.

.. _component_promotion_model_promotion-interface:

PromotionInterface
~~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a **Promotion**.

.. note::

    This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_ and `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_promotion_model_action-interface:

PromotionActionInterface
~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing an **PromotionAction**.

An **PromotionActionInterface** has two defined types by default:

+--------------------------+---------------------+
| Related constant         | Type                |
+==========================+=====================+
| TYPE_FIXED_DISCOUNT      | fixed_discount      |
+--------------------------+---------------------+
| TYPE_PERCENTAGE_DISCOUNT | percentage_discount |
+--------------------------+---------------------+

.. _component_promotion_model_coupon-interface:

CouponInterface
~~~~~~~~~~~~~~~

This interface should be implemented by models representing a **Coupon**.

.. note::

    This interface extends the `CodeAwareInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/CodeAwareInterface.php>`_
    and the `TimestampableInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Model/TimestampableInterface.php>`_.

.. _component_promotion_model_rule-interface:

PromotionRuleInterface
~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by models representing a **PromotionRule**.

A **PromotionRuleInterface** has two defined types by default:

+-----------------------+------------+
| Related constant      | Type       |
+=======================+============+
| TYPE_ITEM_TOTAL       | item_total |
+-----------------------+------------+
| TYPE_ITEM_COUNT       | item_count |
+-----------------------+------------+

.. _component_promotion_model_promotion-countable-subject-interface:

CountablePromotionSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To be able to count the object's promotion subjects, the object class needs to implement
the ``CountablePromotionSubjectInterface``.

.. note::

    This interface extends the :ref:`component_promotion_model_promotion-subject-interface`.

.. _component_promotion_model_promotion-coupon-aware-subject-interface:

PromotionCouponAwarePromotionSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To make the object able to get its associated coupon, the object class needs to implement
the ``PromotionCouponAwarePromotionSubjectInterface``.

.. note::

    This interface extends the :ref:`component_promotion_model_promotion-subject-interface`.

.. _component_promotion_model_promotion-coupons-aware-subject-interface:

PromotionCouponsAwareSubjectInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To make the object able to get its associated coupons collection, the object class needs to implement
the ``PromotionCouponsAwareSubjectInterface``.

.. note::

    This interface extends the :ref:`component_promotion_model_promotion-subject-interface`.


Services Interfaces
-------------------

.. _component_promotion_checker_promotion-eligibility-checker-interface:

PromotionEligibilityCheckerInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Services responsible for checking the promotions eligibility on the promotion subjects should implement this interface.

.. _component_promotion_checker_promotion-rule-checker-interface:

RuleCheckerInterface
~~~~~~~~~~~~~~~~~~~~

Services responsible for checking the rules eligibility should implement this interface.

.. _component_promotion_action_promotion-applicator-interface:

PromotionApplicatorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service responsible for applying promotions in your system should implement this interface.

.. _component_promotion_processor_promotion-processor-interface:

PromotionProcessorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Service responsible for checking all rules and applying configured actions if rules are eligible in your system should implement this interface.

.. _component_promotion_repository_promotion-repository-interface:

PromotionRepositoryInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to be able to find active promotions in your system you should create a repository class which implements this interface.

.. note::
    This interface extends the `RepositoryInterface <https://github.com/Sylius/SyliusResourceBundle/blob/master/src/Component/Repository/RepositoryInterface.php>`_.

.. _component_promotion_generator_coupon-generator-interface:

PromotionCouponGeneratorInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

In order to automate the process of coupon generation your system needs to have a service that will implement this interface.

.. _component_promotion_model_action-action-interface:

PromotionActionCommandInterface
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This interface should be implemented by services that execute actions on the promotion subjects.
