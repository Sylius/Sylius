<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * @var ServiceRegistryInterface
     */
    private $checkerRegistry;
    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;
    /**
     * @var FormTypeRegistryInterface
     */
    private $formTypeRegistry;

    /**
     * @param ServiceRegistryInterface $checkerRegistry
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        ServiceRegistryInterface $checkerRegistry,
        ServiceRegistryInterface $calculatorRegistry,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        $this->checkerRegistry = $checkerRegistry;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zone', ZoneChoiceType::class, [
                'label' => 'sylius.form.shipping_method.zone',
            ])
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.shipping_method.tax_category',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.channels',
            ])
        ;

        $prototypes = [
            'rules' => [],
            'calculators' => [],
        ];

        /** @var RuleCheckerInterface $checker */
        foreach ($this->checkerRegistry->all() as $type => $checker) {
            $prototypes['rules'][$type] = $builder->create('__name__', $checker->getConfigurationFormType())->getForm();
        }

        /** @var CalculatorInterface $calculator */
        foreach ($this->calculatorRegistry->all() as $name => $calculator) {
            $calculatorTypeName = sprintf('sylius_channel_based_shipping_calculator_%s', $calculator->getType());

            if (!$this->formTypeRegistry->has($calculatorTypeName, 'default')) {
                continue;
            }

            $prototypes['calculators'][$name] = $builder->create('configuration', $calculatorTypeName)->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ShippingMethodType::class;
    }
}
