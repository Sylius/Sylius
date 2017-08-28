<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Laurent Paganin-Gioanni <l.paganin@algo-factory.com>
 */
final class SelectAttributeValueTranslationsType extends AbstractType
{
    /**
     * @var string[]
     */
    private $definedLocalesCodes;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(TranslationLocaleProviderInterface $localeProvider)
    {
        $this->definedLocalesCodes = $localeProvider->getDefinedLocalesCodes();
        $this->defaultLocaleCode = $localeProvider->getDefaultLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'entries' => $this->definedLocalesCodes,
            'entry_name' => function ($localeCode) {
                return $localeCode;
            },
            'entry_options' => function ($localeCode) {
                return [
                    'required' => $localeCode === $this->defaultLocaleCode,
                ];
            }
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return FixedCollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_select_attribute_value_translations';
    }
}
