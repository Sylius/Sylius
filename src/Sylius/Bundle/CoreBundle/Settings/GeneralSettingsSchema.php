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
                'locale' => 'en',
                'currency' => 'USD',
                'tracking_code' => '',
            ], $this->defaults))
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
