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
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShippingMethodType extends AbstractResourceType
{
    /**
     * @var string
     */
    protected $shippingMethodTranslationType;

    /**
     * @var ServiceRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * @var ServiceRegistryInterface
     */
    protected $checkerRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    protected $formTypeRegistry;

    /**
     * @var EventSubscriberInterface
     */
    protected $buildShippingMethodFormSubscriber;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $shippingMethodTranslationType
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param ServiceRegistryInterface $checkerRegistry
     * @param FormTypeRegistryInterface $formTypeRegistry
     * @param EventSubscriberInterface $buildShippingMethodFormSubscriber
     */
    public function __construct(
        $dataClass,
        array $validationGroups,
        $shippingMethodTranslationType,
        ServiceRegistryInterface $calculatorRegistry,
        ServiceRegistryInterface $checkerRegistry,
        FormTypeRegistryInterface $formTypeRegistry,
        EventSubscriberInterface $buildShippingMethodFormSubscriber
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->shippingMethodTranslationType = $shippingMethodTranslationType;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->checkerRegistry = $checkerRegistry;
        $this->formTypeRegistry = $formTypeRegistry;
        $this->buildShippingMethodFormSubscriber = $buildShippingMethodFormSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber($this->buildShippingMethodFormSubscriber)
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => $this->shippingMethodTranslationType,
                'label' => 'sylius.form.shipping_method.translations',
            ])
            ->add('position', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.shipping_method.position',
            ])
            ->add('category', ShippingCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => 'sylius.ui.no_requirement',
                'label' => 'sylius.form.shipping_method.category',
            ])
            ->add('categoryRequirement', ChoiceType::class, [
                'choices' => array_flip(ShippingMethod::getCategoryRequirementLabels()),
                'multiple' => false,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.category_requirement',
            ])
            ->add('calculator', CalculatorChoiceType::class, [
                'label' => 'sylius.form.shipping_method.calculator',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.locale.enabled',
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
            $calculatorType = $calculator->getType();

            if (!$this->formTypeRegistry->has($calculatorType, 'default')) {
                continue;
            }

            $form = $builder->create('configuration', $this->formTypeRegistry->get($calculatorType, 'default'));

            $prototypes['calculators'][$name] = $form->getForm();
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
    public function getBlockPrefix()
    {
        return 'sylius_shipping_method';
    }
}
