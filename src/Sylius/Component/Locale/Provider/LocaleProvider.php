<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
     * @var string
     */
    private $fallbackLocaleCode;

    /**
     * @param RepositoryInterface $localeRepository
     * @param string $defaultLocaleCode
     * @param string $fallbackLocaleCode
     */
    public function __construct(RepositoryInterface $localeRepository, $defaultLocaleCode, $fallbackLocaleCode)
    {
        $this->localeRepository = $localeRepository;
        $this->defaultLocaleCode = $defaultLocaleCode;
        $this->fallbackLocaleCode = $fallbackLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocalesCodes()
    {
        $locales = $this->localeRepository->findBy(['enabled' => true]);

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

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocaleCode()
    {
        return $this->fallbackLocaleCode;
    }
}
