<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantType extends BaseProductVariantType
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $calculatorRegistry;

    /**
     * @var FormRegistryInterface
     */
    protected $formRegistry;

    public function __construct(
        $dataClass,
        array $validationGroups = [],
        ServiceRegistryInterface $calculatorRegistry,
        FormRegistryInterface $formRegistry
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->calculatorRegistry = $calculatorRegistry;
        $this->formRegistry = $formRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('price', 'sylius_money', [
                'label' => 'sylius.form.variant.price',
            ])
            ->add('originalPrice', 'sylius_money', [
                'required' => false,
                'label' => 'sylius.form.variant.original_price',
            ])
            ->add('tracked', 'checkbox', [
                'label' => 'sylius.form.variant.tracked',
            ])
            ->add('onHand', 'integer', [
                'label' => 'sylius.form.variant.on_hand',
            ])
            ->add('width', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.width',
            ])
            ->add('height', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.height',
            ])
            ->add('depth', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
            ])
            ->add('weight', 'number', [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
            ])
            ->add('taxCategory', 'sylius_tax_category_choice', [
                'required' => false,
                'empty_value' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
            ])
            ->add('pricingCalculator', 'sylius_price_calculator_choice', [
                'label' => 'sylius.form.shipping_method.calculator',
            ])
        ;

        $prototypes = [
            'calculators' => [],
        ];

        /** @var CalculatorInterface $calculator */
        foreach ($this->calculatorRegistry->all() as $name => $calculator) {
            $calculatorTypeName = sprintf('sylius_price_calculator_%s', $calculator->getType());

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
}
