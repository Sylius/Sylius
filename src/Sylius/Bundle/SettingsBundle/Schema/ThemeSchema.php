<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Schema;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class ThemeSchema implements SchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function configureContext(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'theme',
                'request',
            ])
            ->setAllowedTypes('theme', ThemeInterface::class)
            ->setAllowedTypes('request', Request::class)
        ;
    }

    public function buildSettings(SettingsBuilderInterface $builder)
    {
        // as usual...
    }

    public function buildForm(FormBuilderInterface $builder)
    {
        // as usual...
    }
}
