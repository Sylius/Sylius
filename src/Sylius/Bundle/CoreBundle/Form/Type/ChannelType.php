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
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;
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
            ->add('locales', 'sylius_locale_choice', [
                'label' => 'sylius.form.channel.locales',
                'required' => true,
                'multiple' => true,
            ])
            ->add('defaultLocale', 'sylius_locale_choice', [
                'label' => 'sylius.form.channel.locale_default',
                'required' => true,
                'placeholder' => null,
            ])
            ->add('currencies', ResourceChoiceType::class, [
                'resource' => 'sylius.currency',
                'label' => 'sylius.form.channel.currencies',
                'required' => true,
                'multiple' => true,
            ])
            ->add('defaultTaxZone', ResourceChoiceType::class, [
                'resource' => 'sylius.zone',
                'required' => false,
                'label' => 'sylius.form.channel.tax_zone_default',
            ])
            ->add('taxCalculationStrategy', TaxCalculationStrategyChoiceType::class, [
                'label' => 'sylius.form.channel.tax_calculation_strategy',
            ])
            ->add('themeName', ThemeNameChoiceType::class, [
                'label' => 'sylius.form.channel.theme',
                'required' => false,
                'empty_data' => null,
                'placeholder' => 'sylius.ui.no_theme',
            ])
            ->addEventSubscriber(new AddBaseCurrencySubscriber())
        ;
    }
}
