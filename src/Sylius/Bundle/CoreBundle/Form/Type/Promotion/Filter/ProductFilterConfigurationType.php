<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter;

use Sylius\Bundle\ProductBundle\Form\Type\ProductAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductFilterConfigurationType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $productsToCodesTransformer;

    /**
     * @param DataTransformerInterface $productsToCodesTransformer
     */
    public function __construct(DataTransformerInterface $productsToCodesTransformer)
    {
        $this->productsToCodesTransformer = $productsToCodesTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', ProductAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_filter.products',
                'multiple' => true,
            ])
        ;

        $builder->get('products')->addModelTransformer($this->productsToCodesTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_action_filter_product_configuration';
    }
}
