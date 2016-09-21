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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\BuildProductVariantFormSubscriber;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'required' => false,
                'label' => 'sylius.form.variant.name',
            ])
            ->add('availableOn', 'datetime', [
                'required' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'sylius.form.product_variant.available_on',
            ])
            ->add('availableUntil', 'datetime', [
                'required' => false,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'label' => 'sylius.form.product_variant.available_until',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;

        $builder->addEventSubscriber(new BuildProductVariantFormSubscriber($builder->getFormFactory()));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variant';
    }
}
