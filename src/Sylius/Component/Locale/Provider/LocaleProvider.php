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

use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * @var ResourceRepositoryInterface
     */
    protected $localeRepository;

    /**
     * @var string[]|null
     */
    protected $localeCodes = null;

    /**
     * @param ResourceRepositoryInterface $localeRepository
     *
     * @throws \Exception
     */
    public function __construct(ResourceRepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        if (null === $this->localeCodes) {
            $this->localeCodes = $this->getEnabledLocaleCodes();
        }

        return $this->localeCodes;
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
    protected function getEnabledLocaleCodes()
    {
        $localeCodes = array();

        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findBy(array('enabled' => true));

        foreach ($locales as $locale) {
            $localeCodes[] = $locale->getCode();
        }

        return $localeCodes;
    }
}
