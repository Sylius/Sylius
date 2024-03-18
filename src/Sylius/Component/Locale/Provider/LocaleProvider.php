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

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @param RepositoryInterface<LocaleInterface>|LocaleCollectionProviderInterface $localeRepository
     */
    public function __construct(private LocaleCollectionProviderInterface|RepositoryInterface $localeRepository, private string $defaultLocaleCode)
    {
        if ($this->localeRepository instanceof RepositoryInterface) {
            trigger_deprecation(
                'sylius/locale',
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

    public function getAvailableLocalesCodes(): array
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
     * @return array<array-key, LocaleInterface>
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
