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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Taxonomy rule configuration form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonomyConfigurationType extends AbstractType
{
    protected $validationGroups;

    public function __construct(array $validationGroups)
    {
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxons', 'sylius_taxon_selection', array(
                'label'             => 'sylius.form.rule.taxonomy_configuration.taxons',
                'model_transformer' => array(
                    'save_objects' => false,
                ),
            ))
            ->add('exclude', CheckboxType::class, array(
                'label' => 'sylius.form.rule.taxonomy_configuration.exclude',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
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
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_taxonomy_configuration';
    }
}
