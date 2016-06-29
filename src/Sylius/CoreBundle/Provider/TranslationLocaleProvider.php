<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\Provider;

use Sylius\Locale\Context\LocaleContextInterface;
use Sylius\Resource\Provider\LocaleProviderInterface;
use Sylius\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TranslationLocaleProvider implements LocaleProviderInterface
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentLocale()
    {
        return $this->localeContext->getCurrentLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getFallbackLocale()
    {
        return $this->localeContext->getDefaultLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLocale()
    {
        return $this->localeContext->getDefaultLocale();
    }
}
