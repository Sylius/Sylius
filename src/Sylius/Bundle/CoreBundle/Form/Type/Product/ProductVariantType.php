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

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType as BaseProductVariantType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryChoiceType;
use Sylius\Bundle\TaxationBundle\Form\Type\TaxCategoryChoiceType;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('price', MoneyType::class, [
                'label' => 'sylius.form.variant.price',
            ])
            ->add('tracked', CheckboxType::class, [
                'label' => 'sylius.form.variant.tracked',
            ])
            ->add('onHand', IntegerType::class, [
                'label' => 'sylius.form.variant.on_hand',
            ])
            ->add('width', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.width',
                'invalid_message' => 'sylius.product_variant.width.invalid',
            ])
            ->add('height', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.height',
                'invalid_message' => 'sylius.product_variant.height.invalid',
            ])
            ->add('depth', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.depth',
                'invalid_message' => 'sylius.product_variant.depth.invalid',
            ])
            ->add('weight', NumberType::class, [
                'required' => false,
                'label' => 'sylius.form.variant.weight',
                'invalid_message' => 'sylius.product_variant.weight.invalid',
            ])
            ->add('taxCategory', TaxCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.product_variant.tax_category',
            ])
            ->add('pricingCalculator', 'sylius_price_calculator_choice', [
                'label' => 'sylius.form.shipping_method.calculator',
            ])
            ->add('shippingCategory', ShippingCategoryChoiceType::class, [
                'required' => false,
                'placeholder' => 'sylius.ui.no_requirement',
                'label' => 'sylius.form.product_variant.shipping_category',
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
