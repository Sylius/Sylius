<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Form\Type;

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Bundle\VariableProductBundle\Form\DataTransformer\VariantToCombinationTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Variant match form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantMatchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['product']->getOptions() as $i => $option) {
            $builder->add(Urlizer::urlize($option->getName()), 'sylius_option_value_choice', array(
                'label'         => $option->getPresentation(),
                'option'        => $option,
                'property_path' => '['.$i.']'
            ));
        }

        $builder->addModelTransformer(new VariantToCombinationTransformer($options['product']));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setRequired(array(
                'product'
            ))
            ->setAllowedTypes(array(
                'product' => 'Sylius\Bundle\VariableProductBundle\Model\VariableProductInterface'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_variant_match';
    }
}
