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

use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainsProductConfigurationType extends AbstractType
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $variantRepository;

    /**
     * @param ProductVariantRepositoryInterface $variantRepository
     */
    public function __construct(ProductVariantRepositoryInterface $variantRepository)
    {
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variant', 'sylius_product_variant_from_identifier', [
                'label' => 'sylius.form.promotion_action.add_product_configuration.variant',
                'class' => $this->variantRepository->getClassName(),
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('count', 'integer', [
                'label' => 'sylius.form.promotion_rule.cart_quantity_configuration.count',
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('equal', 'checkbox', [
                'label' => 'sylius.form.promotion_rule.cart_quantity_configuration.equal',
                'constraints' => [
                    new Type(['type' => 'bool']),
                ],
            ])
            ->add('exclude', 'checkbox', [
                'label' => 'sylius.form.promotion_rule.contains_product_configuration.exclude',
                'constraints' => [
                    new Type(['type' => 'bool']),
                ],
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
