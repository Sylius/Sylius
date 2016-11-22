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

use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType as BaseShippingMethodType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodType extends BaseShippingMethodType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('zone', ResourceChoiceType::class, [
                'resource' => 'sylius.zone',
                'label' => 'sylius.form.shipping_method.zone',
            ])
            ->add('taxCategory', ResourceChoiceType::class, [
                'resource' => 'sylius.tax_category',
                'required' => false,
                'placeholder' => '---',
                'label' => 'sylius.form.shipping_method.tax_category',
            ])
            ->add('channels', ResourceChoiceType::class, [
                'resource' => 'sylius.channel',
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.shipping_method.channels',
            ])
        ;
    }
}
