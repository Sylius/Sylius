Custom calculators
==================

Sylius ships with several default calculators, but you can easily register your own.

Simple calculators
------------------

All shipping cost calculators implement ``CalculatorInterface``. In our example we'll create a calculator which calls an external API to obtain the shipping cost.

.. code-block:: php

    # src/AppBundle/Shipping/Calculator/DHLCalculator.php
    <?php

    declare(strict_types=1);

    namespace AppBundle\Shipping\Calculator;

    use Sylius\Component\Shipping\Calculator\CalculatorInterface;
    use Sylius\Component\Shipping\Model\ShipmentInterface;

    final class DHLCalculator implements CalculatorInterface
    {
        /**
         * @var DHLService
         */
        private $dhlService;

        /**
         * @param DHLService $dhlService
         */
        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        /**
         * {@inheritdoc}
         */
        public function calculate(ShipmentInterface $subject, array $configuration): int
        {
            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }

        /**
         * {@inheritdoc}
         */
        public function getType(): string
        {
            return 'dhl';
        }
    }

Now, you need to register your new service in container and tag it with ``sylius.shipping_calculator``.

.. code-block:: yaml

    services:
        app.shipping_calculator.dhl:
            class: AppBundle\Shipping\Calculator\DHLCalculator
            arguments: ['@app.dhl_service']
            tags:
                - { name: sylius.shipping_calculator, calculator: dhl, label: "DHL" }

That would be all. This new option ("DHL") will appear on the **ShippingMethod** creation form, in the "calculator" field.

Configurable calculators
------------------------

You can also create configurable calculators, meaning that you can have several **ShippingMethod**'s using same type of calculator, with different settings.

Let's modify the **DHLCalculator**, so that it charges 0 if shipping more than X items.
First step is to create a form type which will be displayed if our calculator is selected.

.. code-block:: php

    # src/AppBundle/Form/Type/Shipping/Calculator/DHLConfigurationType.php
    <?php

    declare(strict_types=1);

    namespace AppBundle\Form\Type\Shipping\Calculator;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\IntegerType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;

    final class DHLConfigurationType extends AbstractType
    {
        /**
         * {@inheritdoc}
         */
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('limit', IntegerType::class, [
                    'label' => 'Free shipping above total items',
                    'constraints' => [
                        new NotBlank(),
                        new Type(['type' => 'integer']),
                    ]
                ])
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver
                ->setDefaults([
                    'data_class' => null,
                    'limit' => 10,
                ])
                ->setAllowedTypes('limit', 'integer')
            ;
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix(): string
        {
            return 'app_shipping_calculator_dhl';
        }
    }

We also need to register the form type in the container and set this form type in the definition of the calculator.

.. code-block:: yaml

    services:
        app.shipping_calculator.dhl:
            class: AppBundle\Shipping\Calculator\DHLCalculator
            arguments: ['@app.dhl_service']
            tags:
                - { name: sylius.shipping_calculator, calculator: dhl, form_type: AppBundle\Form\Type\Shipping\Calculator\DHLConfigurationType, label: "DHL" }

        app.form.type.shipping_calculator.dhl:
            class: AppBundle\Form\Type\Shipping\Calculator\DHLConfigurationType
            tags:
                - { name: form.type }

Perfect, now we're able to use the configuration inside the ``calculate`` method.

.. code-block:: php

    # src/AppBundle/Shipping/Calculator/DHLCalculator.php
    <?php

    declare(strict_types=1);

    namespace AppBundle\Shipping\Calculator;

    use Sylius\Component\Shipping\Calculator\CalculatorInterface;
    use Sylius\Component\Shipping\Model\ShipmentInterface;

    final class DHLCalculator implements CalculatorInterface
    {
        /**
         * @var DHLService
         */
        private $dhlService;

        /**
         * @param DHLService $dhlService
         */
        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        /**
         * {@inheritdoc}
         */
        public function calculate(ShipmentInterface $subject, array $configuration): int
        {
            if ($subject->getShippingUnitCount() > $configuration['limit']) {
                return 0;
            }

            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }

        /**
         * {@inheritdoc}
         */
        public function getType(): string
        {
            return 'dhl';
        }
    }

Your new configurable calculator is ready to use. When you select the "DHL" calculator in **ShippingMethod** form, configuration fields will appear automatically.
