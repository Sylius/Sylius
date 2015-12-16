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
            ->add('variant', 'sylius_entity_to_identifier', array(
                'label'         => 'sylius.form.action.add_product_configuration.variant',
                'class'         => $this->variantRepository->getClassName(),
                'query_builder' => function () {
                    return $this->variantRepository->getFormQueryBuilder();
                },
                'constraints'   => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('quantity', 'integer', array(
                'label' => 'sylius.form.action.add_product_configuration.quantity',
                'empty_data'  => 1,
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('price', 'sylius_money', array(
                'label' => 'sylius.form.action.add_product_configuration.price',
                'empty_data'  => 0,
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
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
