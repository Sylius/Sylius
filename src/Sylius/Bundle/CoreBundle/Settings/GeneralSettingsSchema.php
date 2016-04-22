<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Settings;

use Sylius\Bundle\SettingsBundle\Schema\SchemaInterface;
use Sylius\Bundle\SettingsBundle\Schema\SettingsBuilderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Locale;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GeneralSettingsSchema implements SchemaInterface
{
    /**
     * @var array
     */
    protected $defaults;

    /**
     * @param array $defaults
     */
    public function __construct(array $defaults = [])
    {
        $this->defaults = $defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function buildSettings(SettingsBuilderInterface $builder)
    {
        $builder
            ->setDefaults(array_merge([
                'title' => 'Sylius - Modern ecommerce for Symfony2',
                'meta_keywords' => 'symfony, sylius, ecommerce, webshop, shopping cart',
                'meta_description' => 'Sylius is modern ecommerce solution for PHP. Based on the Symfony2 framework.',
                'locale' => 'en',
                'currency' => 'USD',
                'tracking_code' => '',
            ], $this->defaults))
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('meta_keywords', 'string')
            ->setAllowedTypes('meta_description', 'string')
            ->setAllowedTypes('locale', 'string')
            ->setAllowedTypes('currency', 'string')
            ->setAllowedTypes('tracking_code', ['null', 'string'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('title', 'text', [
                'label' => 'sylius.form.settings.general.title',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('meta_keywords', 'text', [
                'label' => 'sylius.form.settings.general.meta_keywords',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('meta_description', 'textarea', [
                'label' => 'sylius.form.settings.general.meta_description',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('locale', 'locale', [
                'label' => 'sylius.form.settings.general.locale',
                'constraints' => [
                    new NotBlank(),
                    new Locale(),
                ],
            ])
            ->add('currency', 'sylius_currency_code_choice', [
                'label' => 'sylius.form.settings.general.currency',
                'constraints' => [
                    new NotBlank(),
                    new Currency(),
                ],
            ])
            ->add('tracking_code', 'textarea', [
                'label' => 'sylius.form.settings.general.tracking_code',
            ])
        ;
    }
}
