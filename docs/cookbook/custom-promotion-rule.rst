How to add a custom promotion rule?
===================================

Adding new, custom rules to your shop is a common usecase. You can imagine for instance, that you have some customers
in your shop that you distinguish as premium. And for these premium customers you would like to give special promotions.
For that you will need a new PromotionRule that will check if the customer is premium or not.

Create a new promotion rule
---------------------------

The new Rule needs a RuleChecker class:

.. code-block:: php

    <?php

    namespace AppBundle\Promotion\Checker\Rule;

    use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
    use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
    use AppBundle\Promotion\Checker\Rule\PremiumCustomerRuleChecker;

    class PremiumCustomerRuleChecker implements RuleCheckerInterface
    {
        const TYPE = 'premium_customer';

        /**
         * {@inheritdoc}
         */
        public function isEligible(PromotionSubjectInterface $subject, array $configuration)
        {
            return $subject->getCustomer()->isPremium();
        }

        /**
         * {@inheritdoc}
         */
        public function getConfigurationFormType()
        {
            return PremiumCustomerRuleChecker::class;
        }
    }

Prepare a configuration form type for your new rule
---------------------------------------------------

To be able to configure a promotion with your new rule you will need a form type for the admin panel.

Create the configuration form type class:

.. code-block:: php

    <?php

    namespace AppBundle\Form\Type\Rule;

    use Symfony\Component\Form\AbstractType;

    class PremiumCustomerConfigurationType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
            return 'app_promotion_rule_premium_customer_configuration';
        }
    }

And configure it in the ``app/config/services.yml``:

.. code-block:: yaml

    # app/config/services.yml
    app.form.type.promotion_rule.premium_customer_configuration:
        class: AppBundle\Form\Type\Rule\PremiumCustomerConfigurationType
        tags:
            - { name: form.type }


Register the new rule checker as a service in the ``app/config/services.yml``:

.. code-block:: yaml

    # apps/config/services.yml
    app.promotion_rule_checker.premium_customer:
        class: AppBundle\Promotion\Checker\Rule\PremiumCustomerRuleChecker
        tags:
            - { name: sylius.promotion_rule_checker, type: premium_customer, form-type: AppBundle\Form\Type\Rule\PremiumCustomerConfigurationType, label: Premium customer }


That's all. You will now be able to choose the new rule while creating a new promotion.

.. tip::

    Depending on the type of rule that you would like to configure you may need to configure its form fields.
    See how we do it `here for example <https://github.com/Sylius/Sylius/blob/master/src/Sylius/Bundle/PromotionBundle/Form/Type/Rule/ItemTotalConfigurationType.php>`_.

Learn more
----------

* :doc:`Customization Guide </customization/index>`
* :doc:`Promotions Concept Documentation </customization/index>`
