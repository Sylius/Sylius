<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Action;

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductConfigurationType extends AbstractType
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
            ->add('variant', 'sylius_entity_to_identifier', [
                'label' => 'sylius.form.action.add_product_configuration.variant',
                'class' => $this->variantRepository->getClassName(),
                'query_builder' => function () {
                    return $this->variantRepository->getFormQueryBuilder();
                },
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('quantity', 'integer', [
                'label' => 'sylius.form.action.add_product_configuration.quantity',
                'empty_data' => 1,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('price', 'sylius_money', [
                'label' => 'sylius.form.action.add_product_configuration.price',
                'empty_data' => 0,
                'constraints' => [
                    new NotBlank(),
                    new Type(['type' => 'numeric']),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_action_add_product_configuration';
    }
}
