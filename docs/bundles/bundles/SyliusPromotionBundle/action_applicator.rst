How actions are applied ?
=========================

Everything related to this subject is located in ``Sylius\Component\Promotion\Action``.

Actions
-------

Actions can be created by implementing ``PromotionActionCommandInterface``. This interface provides the method ``execute`` which aim is to apply a promotion to its subject. It also provides the method ``getConfigurationFormType`` which has to return the form name related to this action.

Actions have to be defined as services and have to use the tag named ``sylius.promotion_action`` with the attributes ``type`` and ``label``.

As ``SyliusPromotionBundle`` is totally independent, it does not provide actions out of the box.

.. note::

    ``Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand`` from ``Sylius/Sylius-Standard`` is an example of action for a fixed amount discount. The related service is called ``sylius.promotion_action.fixed_discount``.

.. note::

    ``Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand`` from ``Sylius/Sylius-Standard`` is an example of action for a discount based on percentage. The related service is called  ``sylius.promotion_action.percentage_discount``.

Learn more about actions in the :doc:`promotions concept documentation </book/orders/promotions>` and in the :doc:`Cookbook </cookbook/index>`.

Applying actions to promotions
------------------------------

We have seen above how actions can be created. Now let's see how they are applied to their subject.

The ``PromotionApplicator`` is responsible of this via its method ``apply``. This method will ``execute`` all the registered actions of a promotion on a subject.
