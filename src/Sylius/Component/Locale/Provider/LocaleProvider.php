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

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var string
     */
    private $defaultLocaleCode;

    /**
     * @param RepositoryInterface $localeRepository
     * @param string $defaultLocaleCode
     */
    public function __construct(RepositoryInterface $localeRepository, string $defaultLocaleCode)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocalesCodes(): array
    {
        $locales = $this->localeRepository->findAll();

        return array_map(
            function (LocaleInterface $locale) {
                return $locale->getCode();
            },
            $locales
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}
