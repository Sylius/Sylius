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

use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

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
    protected $formSubscriber;

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
     * @param EventSubscriberInterface $formSubscriber
     */
    public function __construct(
        $extendedType,
        ServiceRegistryInterface $calculatorRegistry,
        EventSubscriberInterface $formSubscriber
    ) {
        $this->extendedType = $extendedType;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->formSubscriber = $formSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber($this->formSubscriber)
            ->add('pricingCalculator', 'sylius_price_calculator_choice', [
                'label' => 'sylius.form.priceable.calculator',
            ])
        ;

        $prototypes = [];

        /** @var CalculatorInterface $calculator */
        foreach ($this->calculatorRegistry->all() as $type => $calculator) {
            $formType = sprintf('sylius_price_calculator_%s', $calculator->getType());

            if (!$formType) {
                continue;
            }

            try {
                $prototypes[$type] = $builder->create('pricingConfiguration', $formType)->getForm();
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = [];

        foreach ($form->getConfig()->getAttribute('prototypes') as $type => $prototype) {
            $view->vars['prototypes'][$type] = $prototype->createView($view);
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
