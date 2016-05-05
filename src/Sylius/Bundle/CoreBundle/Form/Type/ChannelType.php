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
            ->add('taxons', 'sylius_taxon_choice', [
                'label' => 'sylius.form.channel.taxons',
                'multiple' => true,
            ])
            ->add('locales', 'sylius_locale_choice', [
                'label' => 'sylius.form.channel.locales',
                'multiple' => true,
            ])
            ->add('defaultLocale', 'sylius_locale_choice', [
                'label' => 'sylius.form.channel.locale_default',
            ])
            ->add('currencies', 'sylius_currency_choice', [
                'label' => 'sylius.form.channel.currencies',
                'multiple' => true,
            ])
            ->add('defaultCurrency', 'sylius_currency_choice', [
                'label' => 'sylius.form.channel.currency_default',
            ])
            ->add('themeName', 'sylius_theme_name_choice', [
                'label' => 'sylius.form.channel.theme',
                'required' => false,
                'empty_data' => null,
                'empty_value' => 'sylius.ui.no_theme',
            ])
            ->add('shippingMethods', 'sylius_shipping_method_choice', [
                'label' => 'sylius.form.channel.shipping_methods',
                'multiple' => true,
            ])
            ->add('paymentMethods', 'sylius_payment_method_choice', [
                'label' => 'sylius.form.channel.payment_methods',
                'multiple' => true,
            ])
        ;
    }
}
