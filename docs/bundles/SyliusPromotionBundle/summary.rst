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
                    model:      Sylius\Promotion\Model\Promotion
                    interface:  Sylius\Promotion\Model\PromotionInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\PromotionBundle\Form\Type\PromotionType
                        choice:  Sylius\ResourceBundle\Form\Type\ResourceChoiceType
                validation_groups:
                    default: [ sylius ]
            promotion_rule:
                classes:
                    model:      Sylius\Promotion\Model\Rule
                    interface:  Sylius\Promotion\Model\RuleInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\PromotionBundle\Form\Type\RuleType
                validation_groups:
                    default: [ sylius ]
            promotion_action:
                classes:
                    model:      Sylius\Promotion\Model\Action
                    interface:  Sylius\Promotion\Model\ActionInterface
                    controller: Sylius\ResourceBundle\Controller\ResourceController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\PromotionBundle\Form\Type\ActionType
                validation_groups:
                    default: [ sylius ]
            promotion_coupon:
                classes:
                    model:      Sylius\Promotion\Model\Coupon
                    interface:  Sylius\Promotion\Model\CouponInterface
                    controller: Sylius\PromotionBundle\Controller\CouponController
                    repository: ~
                    factory:    Sylius\Resource\Factory\Factory
                    form:
                        default: Sylius\PromotionsBundle\Form\Type\CouponType
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
