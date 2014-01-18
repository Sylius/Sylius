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

use Sylius\Bundle\CoreBundle\Repository\VariantRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Free product configuration form.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductConfigurationType extends AbstractType
{
    protected $validationGroups;

    /**
     * Variant repository
     * @var VariantRepository
     */
    protected $variantRepository;

    public function __construct(array $validationGroups, VariantRepository $variantRepository)
    {
        $this->validationGroups  = $validationGroups;
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $variantRepository = $this->variantRepository;

        $builder
            ->add('variant', 'sylius_entity_to_identifier', array(
                'label'         => 'sylius.form.action.add_product_configuration.variant',
                'class'         => $this->variantRepository->getClassName(),
                'query_builder' => function() use($variantRepository) {
                    return $variantRepository->getFormQueryBuilder();
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_action_add_product_configuration';
    }
}
