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

use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Contains product rule configuration form type.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainsProductConfigurationType extends AbstractType
{
    protected $validationGroups;

    /**
     * @var ProductVariantRepositoryInterface
     */
    protected $variantRepository;

    public function __construct(array $validationGroups, ProductVariantRepositoryInterface $variantRepository)
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
                'class'         => $variantRepository->getClassName(),
                'query_builder' => function () use ($variantRepository) {
                    return $variantRepository->getFormQueryBuilder();
                },
                'constraints'   => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('exclude', 'checkbox', array(
                'label' => 'sylius.form.rule.contains_product_configuration.exclude',
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
        return 'sylius_promotion_rule_contains_product_configuration';
    }
}
