<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture\Factory;

use Sylius\Component\Resource\Model\TranslatableInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait TranslatableExampleFactoryTrait
{
    /** @var OptionsResolver */
    private $optionsResolver;

    abstract protected function createTranslation(TranslatableInterface $translatable, string $localeCode, array $options = []): void;

    protected function configureTranslationsOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('translations', [])
            ->setAllowedTypes('translations', ['array'])
        ;
    }

    protected function createTranslations(TranslatableInterface $translatable, array $options = []): void
    {
        foreach ($options['translations'] as $localeCode => $translationOptions) {
            $this->createTranslation(
                $translatable,
                $localeCode,
                $translationOptions
            );
        }
    }
}
