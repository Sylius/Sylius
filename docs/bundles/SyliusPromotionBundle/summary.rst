Summary
=======

.. code-block:: yaml

    sylius_promotion:
        # The driver used for persistence layer.
        driver: ~
        resources:
            promotion_subject:
                classes:
                    model: ~
            promotion:
                classes:
                    model:      Sylius\Component\Promotion\Model\Promotion
                    interface:  Sylius\Component\Promotion\Model\PromotionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\PromotionBundle\Form\Type\PromotionType
                        choice:  Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            promotion_rule:
                classes:
                    model:      Sylius\Component\Promotion\Model\Rule
                    interface:  Sylius\Component\Promotion\Model\RuleInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\PromotionBundle\Form\Type\RuleType
                validation_groups:
                    default: [ sylius ]
            promotion_action:
                classes:
                    model:      Sylius\Component\Promotion\Model\Action
                    interface:  Sylius\Component\Promotion\Model\ActionInterface
                    controller: Sylius\Bundle\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\PromotionBundle\Form\Type\ActionType
                validation_groups:
                    default: [ sylius ]
            promotion_coupon:
                classes:
                    model:      Sylius\Component\Promotion\Model\Coupon
                    interface:  Sylius\Component\Promotion\Model\CouponInterface
                    controller: Sylius\Bundle\PromotionBundle\Controller\CouponController
                    repository: ~
                    factory:    Sylius\Component\Resource\Factory\Factory
                    form:
                        default: Sylius\Bundle\PromotionsBundle\Form\Type\CouponType
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
