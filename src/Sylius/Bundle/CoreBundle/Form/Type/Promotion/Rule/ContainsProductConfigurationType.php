<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule;

use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ContainsProductConfigurationType extends AbstractType
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_code', 'sylius_product_from_identifier', [
                'label' => 'sylius.form.promotion_action.add_product_configuration.product',
                'class' => $this->productRepository->getClassName(),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'string']),
                ],
                'identifier' => 'code',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_contains_product_configuration';
    }
}
