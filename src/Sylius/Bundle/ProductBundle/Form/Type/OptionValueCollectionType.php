<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Component\Product\Model\OptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This is special collection type, inspired by original 'collection' type
 * implementation, designed to handle option values assigned to object variant.
 * Array of OptionInterface objects should be passed as 'options' option to build proper
 * set of choice types with option values list.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OptionValueCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['options'] as $i => $option) {
            if (!$option instanceof OptionInterface) {
                throw new InvalidConfigurationException('Each object passed as option list must implement "Sylius\Component\Product\Model\OptionInterface"');
            }

            $builder->add((string) $option->getId(), 'sylius_product_option_value_choice', [
                'label' => $option->getName() ?: $option->getCode(),
                'option' => $option,
                'property_path' => '['.$i.']',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('options')
            ->setAllowedTypes('options', 'array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_option_value_collection';
    }
}
