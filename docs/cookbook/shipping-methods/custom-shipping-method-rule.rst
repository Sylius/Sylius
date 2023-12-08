How to add a custom shipping method rule?
=========================================

Shipping method rules are used to show shipping methods if certain criteria are fulfilled.

Say you have a requirement for one of your shipping methods that the volume of the shipment may not exceed 1 cubic meter (i.e. 1 m x 1 m 1 m).
To model this requirement you can use a shipping method rule.

Create a new shipping method rule
---------------------------------

The new rule needs a RuleChecker class:

.. code-block:: php

    <?php

    namespace App\Shipping\Checker\Rule;

    use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
    use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

    final class TotalVolumeLessThanOrEqualRuleChecker implements RuleCheckerInterface
    {
        public const TYPE = 'total_volume_less_than_or_equal';

        public function isEligible(ShippingSubjectInterface $shippingSubject, array $configuration): bool
        {
            return $shippingSubject->getShippingVolume() <= $configuration['volume'];
        }
    }


Prepare a configuration form type for your new rule
---------------------------------------------------

To be able to configure a shipping method with your new rule you will need a form type for the admin panel.

Create the configuration form type class:

.. code-block:: php

    <?php

    namespace App\Form\Type\Rule;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\GreaterThan;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;

    class TotalVolumeLessThanOrEqualConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder->add('volume', NumberType::class, [
                'label' => 'app.form.total_volume_less_than_or_equal_configuration.volume',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                    new Type(['type' => 'numeric', 'groups' => ['sylius']]),
                    new GreaterThan(['value' => 0, 'groups' => ['sylius']])
                ],
            ]);
        }

        public function getBlockPrefix()
        {
            return 'app_shipping_method_rule_total_volume_less_than_or_equal_configuration';
        }
    }

Register the new rule checker as a service in the ``config/services.yaml``:

.. code-block:: yaml

    # config/services.yml
    app.shipping_method_rule_checker.total_volume_less_than_or_equal:
        class: App\Shipping\Checker\Rule\TotalVolumeLessThanOrEqualRuleChecker
        tags:
            - { name: sylius.shipping_method_rule_checker, type: total_volume_less_than_or_equal, form_type: App\Form\Type\Rule\TotalVolumeLessThanOrEqualConfigurationType, label: app.form.shipping_method_rule.total_volume_less_than_or_equal }


That's all. You will now be able to choose the new rule while creating a new shipping method.
