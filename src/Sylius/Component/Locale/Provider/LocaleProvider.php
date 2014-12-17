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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * Default provider returns all enabled locales.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * Repository for locale model.
     *
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * Default locale
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param RepositoryInterface $localeRepository
     * @param string              $defaultLocale
     *
     * @throws \Exception
     */
    public function __construct(RepositoryInterface $localeRepository, $defaultLocale)
    {
        if (empty($defaultLocale)) {
            throw new \Exception('Default locale is required!');
        }

        $this->localeRepository = $localeRepository;

        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        return $this->localeRepository->findBy(array('enabled' => true));
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        return array_map(function (LocaleInterface $locale) {
            return $locale->getCode();
        }, $this->getAvailableLocales());
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredLocales()
    {
        return array($this->defaultLocale);
    }
}
