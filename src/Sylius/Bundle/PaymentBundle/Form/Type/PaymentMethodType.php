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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;

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
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_method';
    }
}
