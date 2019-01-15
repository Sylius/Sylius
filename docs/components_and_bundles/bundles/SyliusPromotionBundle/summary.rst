.. rst-class:: outdated

Summary
=======

.. danger::

   We're sorry but **this documentation section is outdated**. Please have that in mind when trying to use it.
   You can help us making documentation up to date via Sylius Github. Thank you!

.. code-block:: yaml

    sylius_promotion:
        driver: doctrine/orm
        resources:
            promotion_subject:
                classes:
                    model: Sylius\Component\Core\Model\Order
            promotion:
                classes:
                    model:      Sylius\Component\Promotion\Model\Promotion
                    interface:  Sylius\Component\Promotion\Model\PromotionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\PromotionBundle\Form\Type\PromotionType
            promotion_rule:
                classes:
                    factory: Sylius\Component\Core\Factory\PromotionRuleFactory
                    model: Sylius\Component\Promotion\Model\PromotionRule
                    interface: Sylius\Component\Promotion\Model\PromotionRuleInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType
            promotion_coupon:
                classes:
                    model:      Sylius\Component\Promotion\Model\PromotionAction
                    interface:  Sylius\Component\Promotion\Model\PromotionActionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType
            promotion_action:
                classes:
                    model:      Sylius\Component\Promotion\Model\Coupon
                    interface:  Sylius\Component\Promotion\Model\CouponInterface
                    controller: Sylius\Bundle\PromotionBundle\Controller\PromotionCouponController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form: Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
