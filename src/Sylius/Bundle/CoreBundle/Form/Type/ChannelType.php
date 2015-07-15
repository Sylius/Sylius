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

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType as BaseChannelType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Configurable channel form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelType extends BaseChannelType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('taxonomies', 'sylius_taxonomy_choice', array(
                'label'    => 'sylius.form.channel.taxonomies',
                'multiple' => true
            ))
            ->add('locales', 'sylius_locale_choice', array(
                'label'    => 'sylius.form.channel.locales',
                'multiple' => true
            ))
            ->add('currencies', 'sylius_currency_choice', array(
                'label'    => 'sylius.form.channel.currencies',
                'multiple' => true
            ))
            ->add('shippingMethods', 'sylius_shipping_method_choice', array(
                'label'    => 'sylius.form.channel.shipping_methods',
                'multiple' => true
            ))
            ->add('paymentMethods', 'sylius_payment_method_choice', array(
                'label'    => 'sylius.form.channel.payment_methods',
                'multiple' => true
            ))
        ;
    }
}
