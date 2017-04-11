<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
final class SelectAttributeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (is_array($options['configuration'])
            && isset($options['configuration']['multiple'])
            && !$options['configuration']['multiple']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function ($array) {
                    if (count($array) > 0) {
                        return $array[0];
                    }

                    return null;
                },
                function ($string) {
                    if (!is_null($string)) {
                        return [$string];
                    }

                    return [];
                }
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('configuration')
            ->setDefault('placeholder', 'sylius.form.attribute_type_configuration.select.choose')
            ->setNormalizer('choices', function (Options $options) {
                if (is_array($options['configuration'])
                    && isset($options['configuration']['choices'])
                    && is_array($options['configuration']['choices'])) {
                    $choices = array_flip($options['configuration']['choices']);
                    ksort($choices);

                    return $choices;
                }

                return [];
            })
            ->setNormalizer('multiple', function (Options $options) {
                if (is_array($options['configuration']) && isset($options['configuration']['multiple'])) {
                    return $options['configuration']['multiple'];
                }

                return false;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_attribute_type_select';
    }
}
