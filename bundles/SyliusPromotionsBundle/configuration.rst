Configuration reference
=======================

.. code-block:: yaml

    sylius_promotions:
        driver: ~ # The driver used for persistence layer.
        classes:
            promotion:
                model: Sylius\Bundle\PromotionsBundle\Model\Promotion
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionsBundle\Form\Type\PromotionType
            promotion_rule:
                model: Sylius\Bundle\PromotionsBundle\Model\Rule
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionsBundle\Form\Type\RuleType
            promotion_action:
                model: Sylius\Bundle\PromotionsBundle\Model\Action
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionsBundle\Form\Type\ActionType
            promotion_coupon:
                model: Sylius\Bundle\PromotionsBundle\Model\Coupon
                controller: Sylius\Bundle\PromotionsBundle\Controller\CouponController
                repository: ~
                form: Sylius\Bundle\PromotionsBundle\Form\Type\CouponType
        validation_groups:
            promotion: [sylius]
            promotion_rule: [sylius]
            promotion_coupon: [sylius]
            promotion_action: [sylius]
            promotion_rule_item_total_configuration: [sylius]
            promotion_rule_item_count_configuration: [sylius]
            promotion_action_fixed_discount_configuration: [sylius]
            promotion_action_percentage_discount_configuration: [sylius]
            promotion_coupon_generate_instruction: [sylius]
