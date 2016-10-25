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

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ProductFilterConfigurationType extends AbstractType
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DataTransformerInterface
     */
    private $productsToCodesTransformer;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataTransformerInterface $productsToCodesTransformer
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DataTransformerInterface $productsToCodesTransformer
    ) {
        $this->productRepository = $productRepository;
        $this->productsToCodesTransformer = $productsToCodesTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products', 'sylius_product_choice', [
                'label' => 'sylius.form.promotion_rule.product.products',
                'multiple' => true,
                'required' => false,
            ])
        ;

        $builder->get('products')->addModelTransformer($this->productsToCodesTransformer);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_filter_product_configuration';
    }
}
