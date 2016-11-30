<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class TranslationLocaleProvider implements TranslationLocaleProviderInterface
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
    public function __construct(RepositoryInterface $localeRepository, $defaultLocaleCode)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $defaultLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedLocalesCodes()
    {
        $locales = $this->localeRepository->findAll();

        return array_map(
            function (LocaleInterface $locale) { return $locale->getCode(); },
            $locales
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocaleCode()
    {
        return $this->defaultLocaleCode;
    }
}
