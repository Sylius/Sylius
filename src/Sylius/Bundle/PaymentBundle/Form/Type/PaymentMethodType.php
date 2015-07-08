<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Sylius\Bundle\PaymentBundle\Form\Type\EventListener\BuildPaymentMethodFeeCalculatorFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Payment method form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethodType extends AbstractResourceType
{
    /**
     * Fee calculator registry.
     *
     * @var ServiceRegistryInterface
     */
    protected $feeCalculatorRegistry;

    /**
     * Constructor.
     *
     * @param string                   $dataClass
     * @param array                    $validationGroups
     * @param ServiceRegistryInterface $feeCalculatorRegistry
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        ServiceRegistryInterface $feeCalculatorRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->feeCalculatorRegistry = $feeCalculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.payment_method.name',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.payment_method.description',
            ))
            ->add('gateway', 'sylius_payment_gateway_choice', array(
                'label' => 'sylius.form.payment_method.gateway',
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
                'label'    => 'sylius.form.payment_method.enabled',
            ))
            ->add('feeCalculator', 'sylius_fee_calculator_choice', array(
                'label' => 'sylius.form.payment.fee_calculator',
            ))
            ->addEventSubscriber(new BuildPaymentMethodFeeCalculatorFormSubscriber($this->feeCalculatorRegistry, $builder->getFormFactory()))
        ;

        $this->setBuilderFeeCalculatorsConfigurationsAttribute($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['feeCalculatorsConfigurations'] = array();

        foreach ($form->getConfig()->getAttribute('feeCalculatorsConfigurations') as $type => $feeCalculatorConfiguration) {
            $view->vars['feeCalculatorsConfigurations'][$type] = $feeCalculatorConfiguration->createView($view);
        }
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function setBuilderFeeCalculatorsConfigurationsAttribute(FormBuilderInterface $builder)
    {
        $feeCalculatorsConfigurations = array();

        foreach ($this->feeCalculatorRegistry->all() as $type => $feeCalculator) {
            $formType = sprintf('sylius_fee_calculator_%s', $feeCalculator->getType());

            try {
                $feeCalculatorsConfigurations[$type] = $builder->create('feeCalculatorConfiguration', $formType)->getForm();
            } catch (\InvalidArgumentException $exception) {
                continue;
            }
        }

        $builder->setAttribute('feeCalculatorsConfigurations', $feeCalculatorsConfigurations);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_method';
    }
}
