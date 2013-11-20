<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormListener;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethod;
use Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Shipping method form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethodType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Calculator registry.
     *
     * @var CalculatorRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * Rule checker registry.
     *
     * @var RuleCheckerRegistryInterface
     */
    protected $checkerRegistry;

    /**
     * Constructor.
     *
     * @param string                       $dataClass
     * @param array                        $validationGroups
     * @param CalculatorRegistryInterface  $calculatorRegistry
     * @param RuleCheckerRegistryInterface $checkerRegistry
     */
    public function __construct($dataClass, array $validationGroups, CalculatorRegistryInterface $calculatorRegistry, RuleCheckerRegistryInterface $checkerRegistry)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->checkerRegistry = $checkerRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildShippingMethodFormListener($this->calculatorRegistry, $builder->getFormFactory()))
            ->add('name', 'text', array(
                'label' => 'sylius.form.shipping_method.name'
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
                'label'    => 'sylius.form.shipping_method.enabled'
            ))
            ->add('category', 'sylius_shipping_category_choice', array(
                'required' => false,
                'label'    => 'sylius.form.shipping_method.category'
            ))
            ->add('categoryRequirement', 'choice', array(
                'choices'  => ShippingMethod::getCategoryRequirementLabels(),
                'multiple' => false,
                'expanded' => true,
                'label'    => 'sylius.form.shipping_method.category_requirement'
            ))
            ->add('calculator', 'sylius_shipping_calculator_choice', array(
                'label'    => 'sylius.form.shipping_method.calculator'
            ))
        ;

        $prototypes = array();
        $prototypes['rules'] = array();
        foreach ($this->checkerRegistry->getCheckers() as $type => $checker) {
            $prototypes['rules'][$type] = $builder->create('__name__', $checker->getConfigurationFormType())->getForm();
        }
        $prototypes['calculators'] = array();
        foreach ($this->calculatorRegistry->getCalculators() as $name => $calculator) {
            if (!$calculator->isConfigurable()) {
                continue;
            }
            $prototypes['calculators'][$name] = $builder->create('configuration', $calculator->getConfigurationFormType())->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = array();
        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
                $view->vars['prototypes'][$group.'_'.$type] = $prototype->createView($view);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_method';
    }
}
