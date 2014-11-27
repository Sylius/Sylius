<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Provider;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Locale\Provider\LocaleProvider as BaseLocaleProvider;


/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleFormProvider extends BaseLocaleProvider implements LocaleProviderInterface
{
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
            throw new \Exception('Default locale is required');
        }
        parent::__construct($localeRepository);
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocales()
    {
        $locales = array();
        $availableLocales = $this->getAvailableLocales();

        if (0 == count($availableLocales)) {
            return array($this->getDefaultLocale());
        }else{
            foreach ($availableLocales as $availableLocale) {
                $locales[] = $availableLocale->getCode();
            }
        }

        return $locales;
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