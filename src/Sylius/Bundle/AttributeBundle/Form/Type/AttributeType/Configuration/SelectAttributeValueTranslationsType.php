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

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType\Configuration;

use Sylius\Bundle\ResourceBundle\Form\Type\FixedCollectionType;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectAttributeValueTranslationsType extends AbstractType
{
    public function __construct(private TranslationLocaleProviderInterface $localeProvider)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entries' => fn (Options $options): array => $this->localeProvider->getDefinedLocalesCodes(),
            'entry_name' => fn (string $localeCode): string => $localeCode,
            'entry_options' => fn (string $localeCode): array => [
                'required' => $localeCode === $this->localeProvider->getDefaultLocaleCode(),
            ],
        ]);
    }

    public function getParent(): string
    {
        return FixedCollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_select_attribute_value_translations';
    }
}
