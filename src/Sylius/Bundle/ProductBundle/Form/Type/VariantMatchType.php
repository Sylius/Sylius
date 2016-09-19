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

use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Bundle\ProductBundle\Form\DataTransformer\VariantToCombinationTransformer;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantMatchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['product']->getOptions() as $i => $option) {
            $builder->add($option->getCode(), 'sylius_product_option_value_choice', [
                'label' => $option->getName(),
                'option' => $option,
                'property_path' => '['.$i.']',
            ]);
        }

        $builder->addModelTransformer(new VariantToCombinationTransformer($options['product']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('product')
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variant_match';
    }
}
