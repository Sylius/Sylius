<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\Form\Extension;

use Sylius\Bundle\PricingBundle\Form\EventListener\BuildPriceableFormListener;
use Sylius\Bundle\ResourceBundle\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Priceable form extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PriceableTypeExtension extends AbstractTypeExtension
{
    /**
     * Priceable object form type.
     *
     * @var string
     */
    protected $extendedType;

    /**
     * Calculator registry.
     *
     * @var ServiceRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * Constructor.
     *
     * @param string                   $extendedType
     * @param ServiceRegistryInterface $calculatorRegistry
     */
    public function __construct($extendedType, ServiceRegistryInterface $calculatorRegistry)
    {
        $this->extendedType = $extendedType;
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildPriceableFormListener($this->calculatorRegistry, $builder->getFormFactory()))
            ->add('pricingCalculator', 'sylius_price_calculator_choice', array(
                'label' => 'sylius.form.priceable.calculator'
            ))
        ;

        $prototypes = array();
        $prototypes['calculators'] = array();

        foreach ($this->calculatorRegistry->all() as $type => $calculator) {
            $formType = $calculator->getConfigurationFormType();

            if (!$formType) {
                continue;
            }

            $prototypes['calculators'][$type] = $builder->create('configuration', $formType)->getForm();
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
    public function getExtendedType()
    {
        return $this->extendedType;
    }
}
