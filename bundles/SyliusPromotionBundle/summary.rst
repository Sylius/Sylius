Summary
=======

.. code-block:: yaml

    sylius_promotion:
        # The driver used for persistence layer.
        driver: ~
        classes:
            promotion:
                model: Sylius\Component\Promotion\Model\Promotion
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionBundle\Form\Type\PromotionType
            promotion_rule:
                model: Sylius\Component\Promotion\Model\Rule
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionBundle\Form\Type\RuleType
            promotion_action:
                model: Sylius\Component\Promotion\Model\Action
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                repository: ~
                form: Sylius\Bundle\PromotionBundle\Form\Type\ActionType
            promotion_coupon:
                model: Sylius\Component\Promotion\Model\Coupon
                controller: Sylius\Bundle\PromotionBundle\Controller\CouponController
                repository: ~
                form: Sylius\Bundle\PromotionsBundle\Form\Type\CouponType
            promotion_subject:
                model: ~
                controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
        validation_groups:
            promotion: [sylius]
            promotion_rule: [sylius]
            promotion_coupon: [sylius]
            promotion_action: [sylius]
            promotion_rule_item_total_configuration: [sylius]
            promotion_rule_item_count_configuration: [sylius]
            promotion_rule_user_loyality_configuration: [sylius]
            promotion_rule_shipping_country_configuration: [sylius]
            promotion_rule_taxonomy_configuration: [sylius]
            promotion_rule_nth_order_configuration: [sylius]
            promotion_action_fixed_discount_configuration: [sylius]
            promotion_action_percentage_discount_configuration: [sylius]
            promotion_action_add_product_configuration: [sylius]
            promotion_coupon_generate_instruction: [sylius]
            promotion_action_shipping_discount_configuration: [sylius]


`phpspec2 <http://phpspec.net>`_ examples
-----------------------------------------

.. code-block:: bash

    $ composer install --dev --prefer-dist
    $ bin/phpspec run -fpretty --verbose

Bug tracking
------------

This bundle uses `GitHub issues <https://github.com/Sylius/Sylius/issues>`_.
If you have found bug, please create an issue.
