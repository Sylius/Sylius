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

use Sylius\Bundle\CoreBundle\Repository\VariantRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Variant in cart rule configuration form.
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class VariantInCartConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    protected $validationGroups;

    /**
     * @var VariantRepository
     */
    protected $variantRepository;

    /**
     * Constructor
     *
     * @param array $validationGroups
     * @param VariantRepository $variantRepository
     */
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
                'label'         => 'sylius.form.rule.variant_in_cart_configuration.variant',
                'class'         => $this->variantRepository->getClassName(),
                'query_builder' => function() use($variantRepository) {
                    return $variantRepository->getFormQueryBuilder();
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
        return 'sylius_promotion_rule_variant_in_cart_configuration';
    }
}
