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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContainProductVariantConfigurationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variant', 'sylius_entity_to_identifier', array(
                'label'      => 'sylius.form.rule.contain_product_variant_configuration.variant',
                'class'      => $this->dataClass,
                'identifier' => 'id',
            ))
            ->add('only', 'checkbox', array(
                'label'      => 'sylius.form.rule.contain_product_variant_configuration.only',
            ))
            ->add('exclude', 'checkbox', array(
                'label'      => 'sylius.form.rule.contain_product_variant_configuration.exclude',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => $this->validationGroups,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_contain_product_variant_configuration';
    }
}
