Custom calculators
==================

Sylius ships with several default calculators, but you can easily register your own.

Simple calculators
------------------

All shipping cost calculators implement ``CalculatorInterface``. In our example we'll create a calculator which calls an external API to obtain the shipping cost.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle\Shipping/DHLCalculator.php

    namespace Acme\ShopBundle\Shipping;

    use Acme\ShopBundle\Shipping\DHLService;
    use Sylius\Bundle\ShippingBundle\Calculator\Calculator;
    use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;

    class DHLCalculator extends Calculator
    {
        private $dhlService;

        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        public function calculate(ShippingSubjectInterface $subject, array $configuration)
        {
            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }
    }

Now, you need to register your new service in container and tag it with ``sylius.shipping_calculator``.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="acme.shipping_calculator.dhl" class="Acme\ShopBundle\Shipping\DHLCalculator">
                <argument type="service" id="acme.dhl_service" />
                <tag name="sylius.shipping_calculator" calculator="dhl" label="DHL" />
            </service>
        </services>
    </container>

That would be all. This new option ("DHL") will appear on the **ShippingMethod** creation form, in the "calculator" field.

Configurable calculators
------------------------

You can also create configurable calculators, meaning that you can have several **ShippingMethod**'s using same type of calculator, with different settings.

Let's modify the **DHLCalculator**, so that it charges 0 if shipping more than X items.
First step is to define the configuration options, using the Symfony **OptionsResolver** component.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle\Shipping/DHLCalculator.php

    namespace Acme\ShopBundle\Shipping;

    use Acme\ShopBundle\Shipping\DHLService;
    use Sylius\Bundle\ShippingBundle\Calculator\Calculator;
    use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class DHLCalculator extends Calculator
    {
        private $dhlService;

        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        public function calculate(ShippingSubjectInterface $subject, array $configuration)
        {
            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }

        /**
        * {@inheritdoc}
        */
        public function isConfigurable()
        {
            return true;
        }

        public function setConfiguration(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'limit' => 10
                ))
                ->setAllowedTypes(array(
                    'limit' => array('integer'),
                ))
            ;
        }
    }

Done, we've set the default item limit to 10. Now we have to create a form type which will be displayed if our calculator is selected.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle/Form/Type/Shipping/DHLConfigurationType.php

    namespace Acme\ShopBundle\Form\Type\Shipping;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\NotBlank;
    use Symfony\Component\Validator\Constraints\Type;

    class DHLConfigurationType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('limit', 'integer', array(
                    'label' => 'Free shipping above total items',
                    'constraints' => array(
                        new NotBlank(),
                        new Type(array('type' => 'integer')),
                    )
                ))
            ;
        }

        public function configureOptions(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'data_class' => null
                ))
            ;
        }

        public function getName()
        {
            return 'acme_shipping_calculator_dhl';
        }
    }

We also need to register the form type and the calculator in the container.

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8"?>

    <container xmlns="http://symfony.com/schema/dic/services"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xsi:schemaLocation="http://symfony.com/schema/dic/services
                                   http://symfony.com/schema/dic/services/services-1.0.xsd">

        <services>
            <service id="acme.shipping_calculator.dhl" class="Acme\ShopBundle\Shipping\DHLCalculator">
                <argument type="service" id="acme.dhl_service" />
                <tag name="sylius.shipping_calculator" calculator="dhl" label="DHL" />
            </service>
            <service id="acme.form.type.shipping_calculator.dhl" class="Acme\ShopBundle\Form\Type\Shipping\DHLConfigurationType">
                <tag name="form.type" alias="acme_shipping_calculator_dhl" />
            </service>
        </services>
    </container>

Finally, configure the calculator to use the form, by implementing simple ``getConfigurationFormType`` method.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle\Shipping/DHLCalculator.php

    namespace Acme\ShopBundle\Shipping;

    use Acme\ShopBundle\Shipping\DHLService;
    use Sylius\Bundle\ShippingBundle\Calculator\Calculator;
    use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class DHLCalculator extends Calculator
    {
        private $dhlService;

        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        public function calculate(ShippingSubjectInterface $subject, array $configuration)
        {
            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }

        /**
        * {@inheritdoc}
        */
        public function isConfigurable()
        {
            return true;
        }

        public function setConfiguration(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'limit' => 10
                ))
                ->setAllowedTypes(array(
                    'limit' => array('integer'),
                ))
            ;
        }

        public function getConfigurationFormType()
        {
            return 'acme_shipping_calculator_dhl';
        }
    }

Perfect, now we're able to use the configuration inside the ``calculate`` method.

.. code-block:: php

    <?php

    // src/Acme/ShopBundle\Shipping/DHLCalculator.php

    namespace Acme\ShopBundle\Shipping;

    use Acme\ShopBundle\Shipping\DHLService;
    use Sylius\Bundle\ShippingBundle\Calculator\Calculator;
    use Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;

    class DHLCalculator extends Calculator
    {
        private $dhlService;

        public function __construct(DHLService $dhlService)
        {
            $this->dhlService = $dhlService;
        }

        public function calculate(ShippingSubjectInterface $subject, array $configuration)
        {
            if ($subject->getShippingItemCount() > $configuration['limit']) {
                return 0;
            }

            return $this->dhlService->getShippingCostForWeight($subject->getShippingWeight());
        }

        /**
        * {@inheritdoc}
        */
        public function isConfigurable()
        {
            return true;
        }

        public function setConfiguration(OptionsResolver $resolver)
        {
            $resolver
                ->setDefaults(array(
                    'limit' => 10
                ))
                ->setAllowedTypes(array(
                    'limit' => array('integer'),
                ))
            ;
        }

        public function getConfigurationFormType()
        {
            return 'acme_shipping_calculator_dhl';
        }
    }

Your new configurable calculator is ready to use. When you select the "DHL" calculator in **ShippingMethod** form, configuration fields will appear automatically.
