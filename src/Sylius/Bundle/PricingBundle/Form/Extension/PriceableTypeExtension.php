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

use Sylius\Bundle\PricingBundle\Form\Type\CalculatorChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Calculator\Calculators;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PriceableTypeExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    protected $extendedType;

    /**
     * @var ServiceRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FormTypeRegistryInterface
     */
    protected $formTypeRegistry;

    /**
     * @param string $extendedType
     * @param ServiceRegistryInterface $calculatorRegistry
     * @param FormFactoryInterface $formFactory
     * @param FormTypeRegistryInterface $formTypeRegistry
     */
    public function __construct(
        $extendedType,
        ServiceRegistryInterface $calculatorRegistry,
        FormFactoryInterface $formFactory,
        FormTypeRegistryInterface $formTypeRegistry
    ) {
        $this->extendedType = $extendedType;
        $this->calculatorRegistry = $calculatorRegistry;
        $this->formFactory = $formFactory;
        $this->formTypeRegistry = $formTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pricingCalculator', CalculatorChoiceType::class, [
            'label' => 'sylius.form.priceable.calculator',
        ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $priceable = $event->getData();

            if (null === $priceable) {
                return;
            }

            if (!$priceable instanceof PriceableInterface) {
                throw new UnexpectedTypeException($priceable, PriceableInterface::class);
            }

            $this->addPricingConfigurationField($event->getForm(), $priceable->getPricingCalculator());
        });


        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (empty($data) || !array_key_exists('pricingCalculator', $data)) {
                return;
            }

            $this->addPricingConfigurationField($event->getForm(), $data['pricingCalculator']);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['prototypes'])) {
            $view->vars['prototypes'] = [];
        }

        /** @var CalculatorInterface $calculator */
        foreach ($this->calculatorRegistry->all() as $calculator) {
            $calculatorType = $calculator->getType();

            if (!$this->formTypeRegistry->has($calculatorType, 'default')) {
                continue;
            }

            $view->vars['prototypes'][$calculatorType] = $this->formFactory->createNamed(
                'pricingConfiguration',
                $this->formTypeRegistry->get($calculatorType, 'default')
            )->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return $this->extendedType;
    }

    /**
     * @param FormInterface $form
     * @param string $calculatorType
     */
    protected function addPricingConfigurationField(FormInterface $form, $calculatorType)
    {
        if (!$this->formTypeRegistry->has($calculatorType, 'default')) {
            return;
        }

        $form->add(
            'pricingConfiguration',
            $this->formTypeRegistry->get($calculatorType, 'default'),
            ['auto_initialize' => false]
        );
    }
}
