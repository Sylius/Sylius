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
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * @var string[]|null
     */
    protected $localesCodes = null;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(RepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        if (null === $this->localesCodes) {
            $this->localesCodes = $this->getEnabledLocalesCodes();
        }

        return $this->localesCodes;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocaleAvailable($locale)
    {
        return in_array($locale, $this->getAvailableLocales());
    }

    /**
     * @return string[]
     */
    protected function getEnabledLocalesCodes()
    {
        $localesCodes = [];

        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findBy(['enabled' => true]);
        foreach ($locales as $locale) {
            $localesCodes[] = $locale->getCode();
        }

        return $localesCodes;
    }
}
