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

namespace Sylius\Bundle\AttributeBundle\Form\Type\AttributeType;

use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectAttributeType extends AbstractType
{
    public function __construct(private TranslationLocaleProviderInterface $localeProvider)
    {
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (is_array($options['configuration']) &&
            isset($options['configuration']['multiple']) &&
            !$options['configuration']['multiple']) {
            $builder->addModelTransformer(new CallbackTransformer(
                function (mixed $array): mixed {
                    if (is_array($array) && count($array) > 0) {
                        return $array[0];
                    }

                    return null;
                },
                function (mixed $string): array {
                    if (null !== $string) {
                        return [$string];
                    }

                    return [];
                },
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $defaultLocaleCode = $this->localeProvider->getDefaultLocaleCode();

        $resolver
            ->setRequired('configuration')
            ->setDefault('placeholder', 'sylius.form.attribute_type_configuration.select.choose')
            ->setDefault('locale_code', $defaultLocaleCode)
            ->setNormalizer('choices', function (Options $options) use ($defaultLocaleCode) {
                if (is_array($options['configuration']) &&
                    isset($options['configuration']['choices']) &&
                    is_array($options['configuration']['choices'])) {
                    $choices = [];
                    $localeCode = $options['locale_code'] ?? $defaultLocaleCode;

                    foreach ($options['configuration']['choices'] as $key => $choice) {
                        if (isset($choice[$localeCode]) && '' !== $choice[$localeCode]) {
                            $choices[$key] = $choice[$localeCode];

                            continue;
                        }

                        if (false === isset($choice[$defaultLocaleCode]) || '' === $choice[$defaultLocaleCode]) {
                            continue;
                        }

                        $choices[$key] = $choice[$defaultLocaleCode];
                    }

                    $choices = array_flip($choices);
                    ksort($choices);

                    return $choices;
                }

                return [];
            })
            ->setNormalizer('multiple', function (Options $options): bool {
                if (is_array($options['configuration']) && isset($options['configuration']['multiple'])) {
                    return $options['configuration']['multiple'];
                }

                return false;
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_attribute_type_select';
    }
}
