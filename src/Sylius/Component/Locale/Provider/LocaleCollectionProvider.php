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

final class LocaleCollectionProvider implements LocaleCollectionProviderInterface
{
    /**
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(private RepositoryInterface $localeRepository)
    {
    }

    public function getAll(): array
    {
        $locales = [];

        foreach ($this->localeRepository->findAll() as $locale) {
            $locales[$locale->getCode()] = $locale;
        }

        return $locales;
    }
}
