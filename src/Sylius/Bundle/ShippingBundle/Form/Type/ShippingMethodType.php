<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ShippingMethodType extends AbstractResourceType
{
    /**
     * @var string
     */
    private $shippingMethodTranslationType;

    /**
     * @var ServiceRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @var FormTypeRegistryInterface
     */
    private $formTypeRegistry;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param string $shippingMethodTranslationType
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        string $dataClass,
        array $validationGroups,
        string $shippingMethodTranslationType,
        ServiceRegistryInterface $calculatorRegistry,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->shippingMethodTranslationType = $shippingMethodTranslationType;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                'choices' => [
                    'sylius.form.shipping_method.match_none_category_requirement' => ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_NONE,
                    'sylius.form.shipping_method.match_any_category_requirement' => ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ANY,
                    'sylius.form.shipping_method.match_all_category_requirement' => ShippingMethodInterface::CATEGORY_REQUIREMENT_MATCH_ALL,
                ],
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $method = $event->getData();

                if (null === $method || null === $method->getId()) {
                    return;
                }

                $this->addConfigurationField($event->getForm(), $method->getCalculator());
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (empty($data) || !array_key_exists('calculator', $data)) {
                    return;
                }

                $this->addConfigurationField($event->getForm(), $data['calculator']);
            })
        ;

        $prototypes = [];
        foreach ($this->calculatorRegistry->all() as $name => $calculator) {
            /** @var CalculatorInterface $calculator */
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
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['prototypes'] = [];
        foreach ($form->getConfig()->getAttribute('prototypes') as $group => $prototypes) {
            foreach ($prototypes as $type => $prototype) {
                $view->vars['prototypes'][$group . '_' . $type] = $prototype->createView($view);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_shipping_method';
    }

    /**
     * @param FormInterface $form
     * @param string $calculatorName
     */
    private function addConfigurationField(FormInterface $form, string $calculatorName): void
    {
        /** @var CalculatorInterface $calculator */
        $calculator = $this->calculatorRegistry->get($calculatorName);

        $calculatorType = $calculator->getType();
        if (!$this->formTypeRegistry->has($calculatorType, 'default')) {
            return;
        }

        $form->add('configuration', $this->formTypeRegistry->get($calculatorType, 'default'));
    }
}
