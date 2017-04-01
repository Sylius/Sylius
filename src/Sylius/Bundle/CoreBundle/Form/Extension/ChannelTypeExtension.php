<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddBaseCurrencySubscriber;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\TaxCalculationStrategyChoiceType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Bundle\LocaleBundle\Form\Type\LocaleChoiceType;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ChannelTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('locales', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locales',
                'required' => false,
                'multiple' => true,
            ])
            ->add('defaultLocale', LocaleChoiceType::class, [
                'label' => 'sylius.form.channel.locale_default',
                'required' => true,
                'placeholder' => null,
            ])
            ->add('currencies', CurrencyChoiceType::class, [
                'label' => 'sylius.form.channel.currencies',
                'required' => false,
                'multiple' => true,
            ])
            ->add('defaultTaxZone', ZoneChoiceType::class, [
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
            ->add('contactEmail', EmailType::class, [
                'label' => 'sylius.form.channel.contact_email',
                'required' => false,
            ])
            ->add('skippingShippingStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_shipping_step_allowed',
                'required' => false,
            ])
            ->add('skippingPaymentStepAllowed', CheckboxType::class, [
                'label' => 'sylius.form.channel.skipping_payment_step_allowed',
                'required' => false,
            ])
            ->add('accountVerificationRequired', CheckboxType::class, [
                'label' => 'sylius.form.channel.account_verification_required',
                'required' => false,
            ])
            ->addEventSubscriber(new AddBaseCurrencySubscriber())
            ->addEventSubscriber(new ChannelFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return ChannelType::class;
    }
}
