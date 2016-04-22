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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShippingMethodType extends AbstractResourceType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $checkerRegistry;

    /**
     * @var FormRegistryInterface
     */
    private $formRegistry;

    /**
     * @param string                   $dataClass
     * @param array                    $validationGroups
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param ServiceRegistryInterface $checkerRegistry
     * @param FormRegistryInterface    $formRegistry
     */
    public function __construct($dataClass, array $validationGroups, ServiceRegistryInterface $calculatorRegistry, ServiceRegistryInterface $checkerRegistry, FormRegistryInterface $formRegistry)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->calculatorRegistry = $calculatorRegistry;
        $this->checkerRegistry = $checkerRegistry;
        $this->formRegistry = $formRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildShippingMethodFormSubscriber($this->calculatorRegistry, $builder->getFormFactory(), $this->formRegistry))
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('translations', 'sylius_translations', [
                'type' => 'sylius_shipping_method_translation',
                'label' => 'sylius.form.shipping_method.translations',
            ])
            ->add('category', 'sylius_shipping_category_choice', [
                'required' => false,
                'empty_value' => 'sylius.ui.no_requirement',
                'label' => 'sylius.form.shipping_method.category',
            ])
            ->add('categoryRequirement', 'choice', [
                'choices' => ShippingMethod::getCategoryRequirementLabels(),
                'multiple' => false,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.category_requirement',
            ])
            ->add('calculator', 'sylius_shipping_calculator_choice', [
                'label' => 'sylius.form.shipping_method.calculator',
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'sylius.form.locale.enabled',
            ])
        ;

        $prototypes = [];
        $prototypes['rules'] = [];
        foreach ($this->checkerRegistry->all() as $type => $checker) {
            $prototypes['rules'][$type] = $builder->create('__name__', $checker->getConfigurationFormType())->getForm();
        }
        $prototypes['calculators'] = [];
        foreach ($this->calculatorRegistry->all() as $name => $calculator) {
            $calculatorTypeName = sprintf('sylius_shipping_calculator_%s', $calculator->getType());

            if (!$this->formRegistry->hasType($calculatorTypeName)) {
                continue;
            }

            $prototypes['calculators'][$name] = $builder->create('configuration', $calculatorTypeName)->getForm();
        }

        $builder->setAttribute('prototypes', $prototypes);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['prototypes'] = [];
        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
                $view->vars['prototypes'][$group.'_'.$type] = $prototype->createView($view);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_method';
    }
}
