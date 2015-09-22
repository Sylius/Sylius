<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Product stock form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductStockType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $product = $options['product'];

        if ($product->hasVariants()) {
            $builder
                ->add('variant', 'choice', array(
                    'choice_list' => new ObjectChoiceList($product->getVariants())
                ))
            ;
        }

        $builder
            ->add('quantity', 'integer')
            ->add('location', 'sylius_stock_location_choice')
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
            ->setRequired(array(
                'product'
            ))
            ->setAllowedTypes(array(
                'product' => 'Sylius\Component\Core\Model\ProductInterface'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_stock';
    }
}
