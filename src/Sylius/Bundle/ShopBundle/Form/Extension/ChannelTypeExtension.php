<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Bundle\ThemeBundle\Form\Type\ThemeNameChoiceType;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('themeName', ThemeNameChoiceType::class, [
                'label' => 'sylius.form.channel.theme',
                'required' => false,
                'empty_data' => null,
                'placeholder' => 'sylius.ui.no_theme',
            ]);
    }

    /** {@inheritDoc} */
    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
