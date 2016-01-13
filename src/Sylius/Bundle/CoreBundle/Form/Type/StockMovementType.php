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

use Sylius\Bundle\InventoryBundle\Form\Type\StockMovementType as BaseStockMovementType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementType extends BaseStockMovementType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('stockable');

        $product = $options['product'];

        if ($product->hasVariants()) {
            $builder
                ->add('variant', 'choice', array(
                    'choice_list' => new ArrayChoiceList($product->getVariants()),
                    'choice_label' => function ($variant, $key, $index) {
                        return $variant->__toString();
                    },
                ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'product'
            ])
            ->setAllowedTypes([
                'product' => ProductInterface::class
            ]);
    }
}
