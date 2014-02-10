<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Sylius\Bundle\CoreBundle\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Product in cart rule configuration form.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ProductInCartConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    protected $validationGroups;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Constructor
     *
     * @param array $validationGroups
     * @param ProductRepository $productRepository
     */
    public function __construct(array $validationGroups, ProductRepository $productRepository)
    {
        $this->validationGroups  = $validationGroups;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $productRepository = $this->productRepository;

        $builder
            ->add('product', 'sylius_entity_to_identifier', array(
                'label'         => 'sylius.form.rule.product_in_cart_configuration.product',
                'class'         => $this->productRepository->getClassName(),
                'property'      => 'name',
                'query_builder' => function() use($productRepository) {
                    return $productRepository->getFormQueryBuilder();
                },
                'constraints'   => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_product_in_cart_configuration';
    }
}
