<?php

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (class_exists('\Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType')) {
            $builder->add('themeName', ThemeNameChoiceType::class, [
                'label' => 'sylius.form.channel.theme',
                'required' => false,
                'empty_data' => null,
                'placeholder' => 'sylius.ui.no_theme',
            ]);
        }
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
