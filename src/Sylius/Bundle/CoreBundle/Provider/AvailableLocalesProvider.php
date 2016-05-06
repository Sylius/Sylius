<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Component\Resource\Provider\AvailableLocalesProviderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.org>
 */
class AvailableLocalesProvider implements AvailableLocalesProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    private $localeRepository;

    /**
     * @var array|null
     */
    private $localesCodes = null;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(RepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        if (null !== $this->localesCodes) {
            return $this->localesCodes;
        }

        $this->localesCodes = [];

        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findBy(['enabled' => true]);

        foreach ($locales as $locale) {
            $this->localesCodes[] = $locale->getCode();
        }

        return $this->localesCodes;
    }
}
