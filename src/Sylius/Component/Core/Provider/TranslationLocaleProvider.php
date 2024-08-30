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

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleCollectionProviderInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslationLocaleProvider implements TranslationLocaleProviderInterface
{
    /**
     * @param RepositoryInterface<LocaleInterface>|LocaleCollectionProviderInterface $localeRepository
     */
    public function __construct(private LocaleCollectionProviderInterface|RepositoryInterface $localeRepository, private string $defaultLocaleCode)
    {
        if ($this->localeRepository instanceof RepositoryInterface) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as first argument of "%s" is deprecated. Use an instance of "%s" instead.',
                    RepositoryInterface::class,
                    self::class,
                    LocaleCollectionProviderInterface::class,
                ),
            );
        }
    }

    public function getDefinedLocalesCodes(): array
    {
        $locales = $this->getLocales();

        return array_map(
            function (LocaleInterface $locale) {
                return (string) $locale->getCode();
            },
            $locales,
        );
    }

    /**
     * @return array<LocaleInterface>
     */
    private function getLocales(): array
    {
        if ($this->localeRepository instanceof LocaleCollectionProviderInterface) {
            return $this->localeRepository->getAll();
        }

        return $this->localeRepository->findAll();
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}
